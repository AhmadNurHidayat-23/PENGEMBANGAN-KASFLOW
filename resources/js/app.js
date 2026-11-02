document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Hapus transaksi ini? Perhitungan saldo akan disesuaikan.')) {
            event.preventDefault();
        }
    });
});
