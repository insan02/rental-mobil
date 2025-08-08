function confirmLogout(event) {
    event.preventDefault();
    
    const logoutUrl = event.target.getAttribute('data-url') || event.target.closest('a').getAttribute('data-url');
    const userName = '{{ auth()->user()->name }}';
    
    // Cek apakah SweetAlert2 tersedia
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Konfirmasi Logout',
            html: `<br>Apakah Anda yakin ingin keluar dari sistem?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang logout...',
                    text: 'Mohon tunggu sebentar',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                setTimeout(() => {
                    window.location.href = logoutUrl;
                }, 500);
            }
        });
    } else {
        // Fallback jika SweetAlert2 tidak tersedia
        if (confirm(`Apakah Anda yakin ingin keluar dari sistem?`)) {
            window.location.href = logoutUrl;
        }
    }
}