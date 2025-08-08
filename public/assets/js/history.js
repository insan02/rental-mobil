document.addEventListener('DOMContentLoaded', function() {
    // Handle Delete Button Click for Admin and Customer
    document.querySelectorAll('.form-delete-transaksi').forEach(function(form) {
        
        // Add event listener for form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const isAdmin = submitButton.closest('td').querySelector('.btn-danger[title="Delete"]');
            const isCustomer = submitButton.closest('td').querySelector('.btn-danger[title="Hapus"]');
            
            let titleText = 'Konfirmasi Hapus Transaksi';
            let htmlContent = '';
            
            if (isAdmin) {
                htmlContent = `
                    Apakah Anda yakin ingin menghapus transaksi ini?<br><br>
                    <span style="color: #dc3545;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Data yang dihapus tidak dapat dikembalikan!
                    </span>
                `;
            } else {
                htmlContent = `
                    Apakah Anda yakin ingin menghapus transaksi ini?<br><br>
                    <span style="color: #dc3545;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Data akan dihapus permanen dan tidak dapat dikembalikan!
                    </span>
                `;
            }
            
            Swal.fire({
                title: titleText,
                html: htmlContent,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang memproses penghapusan transaksi',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form
                    form.submit();
                }
            });
        });
    });
    
    // Show success message if exists
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        const successMessage = successAlert.textContent.trim();
        // Remove the icon and button text to get clean message
        const cleanMessage = successMessage.replace(/^\s*.*?\s*/, '').replace(/\s*×\s*$/, '').trim();
        
        if (cleanMessage) {
            setTimeout(() => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: cleanMessage,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                
                // Hide the original alert
                successAlert.style.display = 'none';
            }, 100);
        }
    }
    
    // Show error message if exists
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert) {
        const errorMessage = errorAlert.textContent.trim();
        // Remove the icon and button text to get clean message
        const cleanMessage = errorMessage.replace(/^\s*.*?\s*/, '').replace(/\s*×\s*$/, '').trim();
        
        if (cleanMessage) {
            setTimeout(() => {
                Swal.fire({
                    title: 'Gagal!',
                    text: cleanMessage,
                    icon: 'error',
                    timer: 5000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                
                // Hide the original alert
                errorAlert.style.display = 'none';
            }, 100);
        }
    }
});

// Custom CSS for better SweetAlert2 styling
const style = document.createElement('style');
style.textContent = `
    .swal2-html-container {
        line-height: 1.6;
    }
`;
document.head.appendChild(style);