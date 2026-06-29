if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/urimed/sw.js')
            .then(reg => {
                console.log('UriMed PWA Engine: Service Worker Berhasil Didaftarkan!', reg.scope);
            })
            .catch(err => {
                console.error('UriMed PWA Engine: Registrasi Service Worker Gagal!', err);
            });
    });
}

console.log("UriMed UI Core: Multi-device optimization active.");