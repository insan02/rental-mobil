function previewProfileImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Gunakan selector yang lebih spesifik untuk foto profil di halaman profile
            const img = document.querySelector('.col-md-4 .rounded-circle');
            // Atau bisa juga menggunakan ID yang unik
            // const img = document.getElementById('profile-image');
            
            if (img) {
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
}

function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const passwordIcon = document.getElementById(fieldId + '_icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProfilForm');
    const btnUpdate = document.getElementById('btnUpdateProfil');

    if (form && btnUpdate) {
        btnUpdate.addEventListener('click', function(e) {
            e.preventDefault();

            // Ambil data nama dan email untuk konfirmasi
            const nama = document.getElementById('name').value;
            const email = document.getElementById('email').value;

            // Pesan konfirmasi yang lebih umum dan aman
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                html: `Apakah Anda yakin ingin memperbarui data?<br>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan notifikasi loading (tidak ada perubahan di sini)
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui data profil Anda',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim form
                    form.submit();
                }
            });
        });
    }
    
    // Bagian untuk menampilkan error validasi (tidak ada perubahan di sini)
    const errorMessages = document.body.getAttribute('data-error-messages');
    if (errorMessages) {
        Swal.fire({
            title: 'Gagal Memperbarui',
            html: errorMessages,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});