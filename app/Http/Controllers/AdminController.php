<?php
namespace App\Http\Controllers;

use App\Models\{Category, Document, KpData, User};
use App\Support\Kp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/** Manajemen Data Master (khusus Admin, BR-13). */
class AdminController extends Controller
{
    public function stats()
    {
        return response()->json(['success' => true,
            'users' => User::count(), 'mahasiswa' => User::where('role', 'mahasiswa')->count(), 'dosen' => User::where('role', 'dosen')->count(),
            'documents' => Document::count(), 'archived' => Document::whereIn('status', ['approved', 'archived'])->count(),
            'by_status' => Document::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status')]);
    }

    public function users(Request $r)
    {
        $q = User::query()->when($r->role, fn ($x, $v) => $x->where('role', $v))
            ->when($r->search, fn ($x, $s) => $x->where(fn ($y) => $y->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")));
        return response()->json(['success' => true, 'data' => $q->orderBy('name')->get(['id', 'name', 'email', 'role', 'nim', 'nip', 'phone', 'is_active', 'dosen_id'])]);
    }

    public function storeUser(Request $r)
    {
        $d = $r->validate([...$this->userRules(), 'password' => 'required|min:6'], $this->msg());
        $d['email'] = $d['email'] ?? (($d['nim'] ?? $d['nip']) . '@kp.local');
        $u = User::create($d);
        Kp::audit('pengguna_tambah', $u->name . ' (' . $u->role . ')');
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil ditambahkan', 'user' => $u], 201);
    }

    public function updateUser(Request $r, User $user)
    {
        $d = $r->validate([...$this->userRules($user->id), 'password' => 'nullable|min:6'], $this->msg());
        $d['email'] = $d['email'] ?? $user->email;
        if (blank($d['password'] ?? null)) unset($d['password']);
        $user->update($d);
        Kp::audit('pengguna_ubah', $user->name);
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil diperbarui', 'user' => $user]);
    }

    public function destroyUser(Request $r, User $user)
    {
        abort_if($user->id === $r->user()->id, 422, 'Tidak dapat menghapus akun sendiri.');
        abort_if(Document::where('user_id', $user->id)->exists(), 422, 'Pengguna masih memiliki dokumen (BR-14). Nonaktifkan akun saja.');
        Kp::audit('pengguna_hapus', $user->name);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus']);
    }

    public function categories()
    {
        return response()->json(['success' => true, 'data' => Category::withCount('documents')->orderBy('id')->get()]);
    }

    public function storeCategory(Request $r)
    {
        return response()->json(['success' => true, 'category' => Category::create($this->catData($r))], 201);
    }

    public function updateCategory(Request $r, Category $category)
    {
        $category->update($this->catData($r, $category->id));
        return response()->json(['success' => true, 'category' => $category]);
    }

    public function destroyCategory(Category $category)
    {
        abort_if($category->documents()->exists(), 422, 'Kategori masih dipakai dokumen (BR-14).');
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus']);
    }

    public function storeKp(Request $r)
    {
        $d = $r->validate(['mahasiswa_id' => 'required|exists:users,id', 'perusahaan' => 'required|string|max:150', 'company_id' => 'nullable|exists:companies,id', 'judul' => 'nullable|string|max:200',
            'tanggal_mulai' => 'nullable|date', 'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai']);
        Kp::audit('kp_tambah', $d['perusahaan']);
        return response()->json(['success' => true, 'kp' => KpData::updateOrCreate(['mahasiswa_id' => $d['mahasiswa_id']], $d + ['status' => 'berlangsung'])], 201);
    }

    // Penetapan status KP, perusahaan, dosen pembimbing, dan pembimbing lapangan
    public function updateKp(Request $r, KpData $kp)
    {
        $d = $r->validate(['status' => 'required|in:' . implode(',', array_keys(Kp::KP_STATUS)), 'company_id' => 'nullable|exists:companies,id', 'perusahaan' => 'required|string|max:150',
            'judul' => 'nullable|string|max:200', 'tanggal_mulai' => 'nullable|date', 'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai', 'catatan_admin' => 'nullable|string|max:1000',
            'dosen_id' => 'nullable|exists:users,id', 'lapangan_id' => 'nullable|exists:users,id']);
        $old = $kp->status;
        $kp->update(collect($d)->except(['dosen_id', 'lapangan_id'])->all());
        $kp->mahasiswa->update(['dosen_id' => $d['dosen_id'] ?? null, 'lapangan_id' => $d['lapangan_id'] ?? null]);
        if ($old !== $d['status']) Kp::notify($kp->mahasiswa_id, 'Status KP: ' . Kp::KP_STATUS[$d['status']], $d['catatan_admin'] ?? null, route('kp.pengajuan'));
        Kp::audit('kp_ubah', $kp->mahasiswa->name . ' -> ' . $d['status']);
        return response()->json(['success' => true]);
    }

    public function destroyKp(KpData $kp)
    {
        Kp::audit('kp_hapus', $kp->perusahaan);
        $kp->delete();
        return response()->json(['success' => true]);
    }

    private function msg(): array
    {
        return ['nim.required_if' => 'NPM wajib diisi untuk mahasiswa.', 'nip.required_if' => 'NIP / ID wajib diisi untuk dosen, admin, dan pembimbing lapangan.',
            'nim.digits_between' => 'NPM harus berupa angka (5-20 digit).', 'nip.digits_between' => 'NIP / ID harus berupa angka (5-30 digit).',
            'nim.unique' => 'NPM sudah terdaftar.', 'nip.unique' => 'NIP / ID sudah terdaftar.', 'email.unique' => 'Email sudah dipakai.'];
    }

    private function userRules(?int $id = null): array
    {
        return ['name' => 'required|string|max:100', 'email' => ['nullable', 'email', Rule::unique('users')->ignore($id)],
            'role' => 'required|in:mahasiswa,dosen,admin,lapangan', 'company_id' => 'nullable|exists:companies,id', 'lapangan_id' => 'nullable|exists:users,id',
            'nim' => ['required_if:role,mahasiswa', 'nullable', 'digits_between:5,20', Rule::unique('users', 'nim')->ignore($id)],
            'nip' => ['required_if:role,dosen,admin,lapangan', 'nullable', 'digits_between:5,30', Rule::unique('users', 'nip')->ignore($id)],
            'phone' => 'nullable|string|max:20', 'is_active' => 'boolean', 'dosen_id' => 'nullable|exists:users,id'];
    }

    private function catData(Request $r, ?int $id = null): array
    {
        return $r->validate(['name' => 'required|string|max:100', 'slug' => ['required', 'alpha_dash', Rule::unique('categories')->ignore($id)],
            'description' => 'nullable|string', 'max_file_size' => 'required|integer|min:1|max:100', 'is_active' => 'boolean', 'deadline' => 'nullable|date', 'wajib' => 'boolean']);
    }
}
