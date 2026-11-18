/* Fungsi halaman admin. Membutuhkan kp-pages.js */
const byId = id => document.getElementById(id);
const showModal = id => bootstrap.Modal.getOrCreateInstance(byId(id)).show();
const d10 = v => (v || '').slice(0, 10);

/* ---- Pengguna ---- */
function roleUI() {
    const r = byId('uRole').value;
    byId('uMhsBox').style.display = r === 'mahasiswa' ? '' : 'none';
    byId('uCoBox').style.display = r === 'lapangan' ? '' : 'none';
    byId('uIdLbl').textContent = r === 'mahasiswa' ? 'NPM' : (r === 'dosen' ? 'NIP' : (r === 'lapangan' ? 'ID Pembimbing Lapangan' : 'ID Admin'));
}
function openUser(u, preset) {
    const e = !!u; u = u || {};
    byId('uTitle').textContent = e ? 'Ubah Pengguna' : 'Tambah Pengguna';
    byId('uId').value = u.id || ''; byId('uName').value = u.name || '';
    byId('uEmail').value = (u.email || '').endsWith('@kp.local') ? '' : (u.email || '');
    byId('uRole').value = u.role || preset || 'mahasiswa';
    byId('uNo').value = (u.role === 'mahasiswa' ? u.nim : u.nip) || '';
    byId('uDosen').value = u.dosen_id || ''; byId('uLap').value = u.lapangan_id || ''; byId('uCo').value = u.company_id || '';
    byId('uPw').value = ''; byId('uActive').checked = e ? !!u.is_active : true;
    byId('uPwHint').textContent = e ? '(kosongkan jika tidak diubah)' : '(min. 6 karakter)';
    roleUI(); showModal('uModal');
}
function saveUser(b) {
    const r = byId('uRole').value, id = byId('uId').value, no = byId('uNo').value.trim() || null;
    const body = { name: byId('uName').value, email: byId('uEmail').value.trim() || null, role: r, is_active: byId('uActive').checked,
        nim: r === 'mahasiswa' ? no : null, nip: r !== 'mahasiswa' ? no : null,
        dosen_id: r === 'mahasiswa' ? (byId('uDosen').value || null) : null, lapangan_id: r === 'mahasiswa' ? (byId('uLap').value || null) : null,
        company_id: r === 'lapangan' ? (byId('uCo').value || null) : null };
    if (byId('uPw').value) body.password = byId('uPw').value;
    kpDo(b, () => kp(id ? 'PUT' : 'POST', id ? '/users/' + id : '/users', body));
}

/* ---- Kategori ---- */
function openCat(c) {
    const e = !!c; c = c || {};
    byId('cTitle').textContent = e ? 'Ubah Kategori' : 'Tambah Kategori';
    byId('cId').value = c.id || ''; byId('cName').value = c.name || ''; byId('cSlug').value = c.slug || '';
    byId('cDesc').value = c.description || ''; byId('cMax').value = c.max_file_size || 10; byId('cDeadline').value = d10(c.deadline);
    byId('cWajib').checked = e ? !!c.wajib : true; byId('cActive').checked = e ? !!c.is_active : true;
    showModal('cModal');
}
function saveCat(b) {
    const id = byId('cId').value;
    kpDo(b, () => kp(id ? 'PUT' : 'POST', id ? '/categories/' + id : '/categories', { name: byId('cName').value, slug: byId('cSlug').value,
        description: byId('cDesc').value || null, max_file_size: +byId('cMax').value, deadline: byId('cDeadline').value || null,
        wajib: byId('cWajib').checked, is_active: byId('cActive').checked }));
}

/* ---- Data KP ---- */
function openKp(k) {
    const e = !!k; k = k || {};
    byId('kpTitle').textContent = e ? 'Tinjau / Atur KP — ' + (k.nama || '') : 'Tambah Data KP';
    byId('kId').value = k.id || ''; byId('kM').value = k.mahasiswa_id || byId('kM').options[0]?.value || ''; byId('kM').disabled = e;
    byId('kSt').value = k.status || 'berlangsung'; byId('kC').value = k.company_id || ''; byId('kP').value = k.perusahaan || '';
    byId('kJ').value = k.judul || ''; byId('kS').value = d10(k.tanggal_mulai); byId('kE').value = d10(k.tanggal_selesai);
    byId('kD').value = k.dosen_id || ''; byId('kL').value = k.lapangan_id || ''; byId('kN').value = k.catatan_admin || '';
    showModal('kpModal');
}
function saveKp(b) {
    const id = byId('kId').value, sel = byId('kC'), nm = sel.value ? sel.options[sel.selectedIndex].text : byId('kP').value;
    const body = { perusahaan: nm, company_id: sel.value || null, judul: byId('kJ').value || null, tanggal_mulai: byId('kS').value || null, tanggal_selesai: byId('kE').value || null };
    if (id) Object.assign(body, { status: byId('kSt').value, dosen_id: byId('kD').value || null, lapangan_id: byId('kL').value || null, catatan_admin: byId('kN').value || null });
    else body.mahasiswa_id = +byId('kM').value;
    kpDo(b, () => kp(id ? 'PUT' : 'POST', id ? '/kp/' + id : '/kp', body));
}

/* ---- Perusahaan ---- */
function openCo(c) {
    const e = !!c; c = c || {};
    byId('coTitle').textContent = e ? 'Ubah Perusahaan' : 'Tambah Perusahaan';
    byId('coId').value = c.id || ''; byId('coName').value = c.name || ''; byId('coBidang').value = c.bidang || ''; byId('coAddr').value = c.address || ''; byId('coPhone').value = c.phone || '';
    showModal('coModal');
}
function saveCo(b) {
    const id = byId('coId').value;
    kpDo(b, () => kp(id ? 'PUT' : 'POST', id ? '/companies/' + id : '/companies', { name: byId('coName').value, bidang: byId('coBidang').value || null, address: byId('coAddr').value || null, phone: byId('coPhone').value || null }));
}

/* ---- Pengumuman ---- */
function openAnn(a) {
    const e = !!a; a = a || {};
    byId('aTitleH').textContent = e ? 'Ubah Pengumuman' : 'Buat Pengumuman';
    byId('aId').value = a.id || ''; byId('aTitle').value = a.title || ''; byId('aBody').value = a.body || '';
    showModal('aModal');
}
function saveAnn(b) {
    const id = byId('aId').value;
    kpDo(b, () => kp(id ? 'PUT' : 'POST', id ? '/announcements/' + id : '/announcements', { title: byId('aTitle').value, body: byId('aBody').value }));
}

function del(url, label) { if (confirm('Hapus ' + label + '?')) kpDo(null, () => kp('DELETE', url)); }
