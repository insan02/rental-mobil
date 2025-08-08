document.addEventListener('DOMContentLoaded', function() {
    // Handle Delete Button Click
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('.form-delete');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus data customer:<br><strong>${name}</strong><br><em>(${email})</em>?<br><br><span style="color: #dc3545;">Data yang dihapus tidak dapat dikembalikan!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    
    // Show success message if exists
    const successMessage = document.body.getAttribute('data-success-message');
    if (successMessage) {
        Swal.fire({
            title: 'Berhasil!',
            text: successMessage,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    }
});