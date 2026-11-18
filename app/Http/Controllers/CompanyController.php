<?php
namespace App\Http\Controllers;

use App\Models\{Company, KpData, User};
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return view('pages.admin.companies', ['companies' => Company::withCount(['pembimbing', 'kp'])->orderBy('name')->get()]);
    }

    private function rules(): array
    {
        return ['name' => 'required|string|max:150', 'address' => 'nullable|string|max:500', 'bidang' => 'nullable|string|max:150', 'phone' => 'nullable|string|max:30'];
    }

    public function store(Request $r) { return response()->json(['success' => true, 'company' => Company::create($r->validate($this->rules()))], 201); }

    public function update(Request $r, Company $company) { $company->update($r->validate($this->rules())); return response()->json(['success' => true]); }

    public function destroy(Company $company)
    {
        abort_if(User::where('company_id', $company->id)->exists() || KpData::where('company_id', $company->id)->exists(), 422, 'Perusahaan masih dipakai data KP atau pembimbing lapangan.');
        $company->delete();
        return response()->json(['success' => true]);
    }
}
