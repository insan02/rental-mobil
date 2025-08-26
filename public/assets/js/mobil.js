document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editMobilForm');
    const btnUpdate = document.getElementById('btnUpdate');
    
    btnUpdate.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get form data for display
        const merek = document.getElementById('merek').value;
        const nopolisi = document.getElementById('nopolisi').value;
        
        Swal.fire({
            title: 'Konfirmasi Update',
            html: `Apakah Anda yakin ingin memperbarui data mobil:<br><strong>${merek} (${nopolisi})</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Perbarui!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang memperbarui data mobil',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                form.submit();
            }
        });
    });
    
    // Show validation errors with SweetAlert
    const errorMessages = document.body.getAttribute('data-error-messages');
    if (errorMessages) {
        Swal.fire({
            title: 'Validation Error',
            html: errorMessages,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});

// Function for image preview (if you're using previewfoto.js)
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            
            if (preview && container) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
}

function removePreview() {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    const fileInput = document.getElementById('foto');
    
    if (preview && container && fileInput) {
        preview.src = '';
        container.style.display = 'none';
        fileInput.value = '';
    }
}

// mobil.js - Updated delete confirmation handling

document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button click
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const merek = this.getAttribute('data-merek');
            const nopolisi = this.getAttribute('data-nopolisi');
            const hasActive = this.getAttribute('data-has-active');
            const form = this.closest('form');
            
            // Check if mobil has active transactions
            if (hasActive === 'true') {
                Swal.fire({
                    title: 'Tidak Dapat Dihapus!',
                    text: `Mobil ${merek} (${nopolisi}) sedang dalam transaksi aktif (Wait/Proses). Selesaikan transaksi terlebih dahulu.`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            
            // Show confirmation dialog for deletion
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus mobil ${merek} (${nopolisi})?`,
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
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form
                    form.submit();
                }
            });
        });
    });
});

// Auto hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);