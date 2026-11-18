/* Helper fetch untuk halaman baru: menampilkan pesan validasi Laravel dengan rapi */
async function kp(method, url, body) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const isForm = body instanceof FormData;
    const res = await fetch(API_BASE + url, {
        method, credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, ...(body && !isForm ? { 'Content-Type': 'application/json' } : {}) },
        body: body ? (isForm ? body : JSON.stringify(body)) : undefined,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const first = data.errors ? Object.values(data.errors).flat()[0] : null;
        throw new Error(first || data.message || ('Permintaan gagal (' + res.status + ')'));
    }
    return data;
}
async function kpDo(btn, fn, okMsg) {
    const old = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>'; }
    try { await fn(); location.reload(); }
    catch (e) { alert(e.message); if (btn) { btn.disabled = false; btn.innerHTML = old; } }
}
