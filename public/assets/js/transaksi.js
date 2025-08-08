//create.blade
function calculateTotal() {
    const hargaPerHari = selectedCarData.harga;
    const lamaSewa = document.getElementById('lama').value || 1;
    const total = hargaPerHari * lamaSewa;
    
    document.getElementById('harga-per-hari').textContent = 'Rp ' + hargaPerHari.toLocaleString('id-ID');
    document.getElementById('totalBiaya').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function calculateReturnDate() {
    const tglPesanInput = document.getElementById('tgl_pesan');
    const lamaInput = document.getElementById('lama');
    const tglKembaliDisplay = document.getElementById('tgl_kembali_display');
    
    if (tglPesanInput.value && lamaInput.value) {
        const tglPesan = new Date(tglPesanInput.value);
        const lama = parseInt(lamaInput.value) || 0;
        
        const tglKembali = new Date(tglPesan);
        tglKembali.setDate(tglKembali.getDate() + lama);
        
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        tglKembaliDisplay.value = tglKembali.toLocaleDateString('id-ID', options);
    } else {
        tglKembaliDisplay.value = '';
    }
}

// Handle car selection in modal
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    calculateReturnDate();
    
    // Car selection in modal
    const carOptions = document.querySelectorAll('.car-option');
    let selectedCarOption = null;
    
    carOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            carOptions.forEach(opt => opt.classList.remove('border-primary', 'bg-light'));
            
            // Add selection to current
            this.classList.add('border-primary', 'bg-light');
            selectedCarOption = this;
        });
    });
    
    // Confirm car change
    document.getElementById('confirmCarChange').addEventListener('click', function() {
        if (selectedCarOption) {
            const carData = {
                id: selectedCarOption.dataset.carId,
                merek: selectedCarOption.dataset.carMerek,
                nopolisi: selectedCarOption.dataset.carNopolisi,
                jenis: selectedCarOption.dataset.carJenis,
                kapasitas: selectedCarOption.dataset.carKapasitas,
                harga: parseFloat(selectedCarOption.dataset.carHarga),
                foto: selectedCarOption.dataset.carFoto
            };
            
            // Update form data
            document.getElementById('selected_mobil_id').value = carData.id;
            selectedCarData = carData;
            
            // Update car details display
            document.getElementById('selected-car-image').src = carData.foto;
            document.getElementById('selected-car-merek').textContent = carData.merek;
            document.getElementById('selected-car-nopolisi').textContent = carData.nopolisi;
            document.getElementById('selected-car-kapasitas').textContent = carData.kapasitas;
            document.getElementById('selected-car-harga').textContent = 'Rp ' + parseFloat(carData.harga).toLocaleString('id-ID');
            
            // Update jenis badge
            const jenisElement = document.getElementById('selected-car-jenis');
            jenisElement.textContent = carData.jenis;
            jenisElement.className = 'badge ';
            switch(carData.jenis) {
                case 'Sedan':
                    jenisElement.className += 'bg-info';
                    break;
                case 'MPV':
                    jenisElement.className += 'bg-warning';
                    break;
                case 'SUV':
                    jenisElement.className += 'bg-success';
                    break;
                default:
                    jenisElement.className += 'bg-secondary';
            }
            
            // Recalculate total with new car price
            calculateTotal();
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show mt-3';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>Mobil berhasil diganti ke <strong>${carData.merek} - ${carData.nopolisi}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alert, document.querySelector('.row'));
            
            // Auto remove alert after 3 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 3000);
        }
    });
});

//update status
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const transaksiId = this.dataset.id;
            const newStatus = this.value;
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/transaksis/${transaksiId}/update-status`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }
            
            // Add method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);
            
            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    });

    // Enhanced form submission with SweetAlert confirmation for edit form
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data for validation and display
            const mobil = document.getElementById('mobil_id');
            const lama = document.getElementById('lama').value;
            const tglPesan = document.getElementById('tgl_pesan').value;
            const alamat = document.getElementById('alamat').value.trim();
            const nama = document.getElementById('nama') ? document.getElementById('nama').value.trim() : '';
            const ponsel = document.getElementById('ponsel') ? document.getElementById('ponsel').value.trim() : '';
            
            // Validation checks
            const errors = [];
            
            if (!mobil.value) {
                errors.push('Pilih mobil terlebih dahulu');
            }
            
            if (!lama || parseInt(lama) < 1 || parseInt(lama) > 30) {
                errors.push('Lama sewa harus antara 1-30 hari');
            }
            
            if (!tglPesan) {
                errors.push('Tanggal sewa harus diisi');
            } else {
                // Check if date is not in the past
                const today = new Date();
                const selectedDate = new Date(tglPesan);
                today.setHours(0, 0, 0, 0);
                selectedDate.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    errors.push('Tanggal sewa tidak boleh di masa lalu');
                }
            }
            
            if (!alamat) {
                errors.push('Alamat lengkap harus diisi');
            }
            
            // Check admin-specific fields
            if (document.getElementById('nama') && !nama) {
                errors.push('Nama lengkap harus diisi');
            }
            
            if (document.getElementById('ponsel') && !ponsel) {
                errors.push('No. HP/Email harus diisi');
            }
            
            // If there are validation errors, show them
            if (errors.length > 0) {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    html: '<div class="text-start"><ul class="mb-0">' + 
                          errors.map(error => `<li>${error}</li>`).join('') + 
                          '</ul></div>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return false;
            }
            
            // Get selected car details for confirmation
            const selectedMobil = mobil.options[mobil.selectedIndex];
            const carDetails = selectedMobil ? selectedMobil.textContent : 'Mobil tidak dipilih';
            const totalBiaya = document.getElementById('total-biaya').textContent;
            
            // Format dates for display
            const tglPesanFormatted = new Date(tglPesan).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
            
            const tglKembali = new Date(tglPesan);
            tglKembali.setDate(tglKembali.getDate() + parseInt(lama));
            const tglKembaliFormatted = tglKembali.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Update Transaksi',
                text: 'Apakah Anda yakin ingin memperbarui data transaksi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-save me-1"></i>Ya, Update!',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Update...',
                        html: `
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mb-0">Sedang memperbarui data transaksi</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            // Custom loading animation
                            const popup = Swal.getPopup();
                            if (popup) {
                                const spinner = popup.querySelector('.spinner-border');
                                if (spinner) {
                                    spinner.style.animation = 'spin 1s linear infinite';
                                }
                            }
                        }
                    });
                    
                    // Submit form after a short delay to show the loading animation
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                }
            });
        });
    }
    
    // Show validation errors with SweetAlert if they exist
    const errorMessages = document.body.getAttribute('data-error-messages');
    if (errorMessages) {
        Swal.fire({
            title: 'Kesalahan Validasi',
            html: `
                <div class="text-start">
                    <p class="text-muted mb-2">Terdapat kesalahan pada form:</p>
                    ${errorMessages}
                </div>
            `,
            icon: 'error',
            confirmButtonText: 'Perbaiki Data',
            confirmButtonColor: '#d33',
            width: '450px'
        });
    }

    // Show success message if update was successful
    const successMessage = document.body.getAttribute('data-success-message');
    if (successMessage) {
        Swal.fire({
            title: 'Berhasil!',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#198754'
        });
    }
});

//edit.blade calculations
function calculateTotal() {
    const mobilSelect = document.getElementById('mobil_id');
    const lamaInput = document.getElementById('lama');
    
    const hargaPerHariSpan = document.getElementById('harga-per-hari');
    const lamaSewaSpan = document.getElementById('lama-sewa');
    const totalBiayaSpan = document.getElementById('total-biaya');
    
    if (mobilSelect.value && lamaInput.value) {
        const selectedOption = mobilSelect.options[mobilSelect.selectedIndex];
        const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
        const lama = parseInt(lamaInput.value) || 0;
        const total = harga * lama;
        
        hargaPerHariSpan.textContent = 'Rp ' + harga.toLocaleString('id-ID');
        lamaSewaSpan.textContent = lama;
        totalBiayaSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
}

// Update car image and details when car selection changes
function updateCarImage() {
    const mobilSelect = document.getElementById('mobil_id');
    const carImage = document.getElementById('car-image');
    const carMerek = document.getElementById('car-merek');
    const carNopolisi = document.getElementById('car-nopolisi');
    const carJenis = document.getElementById('car-jenis');
    const carKapasitas = document.getElementById('car-kapasitas');
    
    if (mobilSelect && mobilSelect.value) {
        const selectedOption = mobilSelect.options[mobilSelect.selectedIndex];
        const foto = selectedOption.getAttribute('data-foto');
        const merek = selectedOption.getAttribute('data-merek');
        const nopolisi = selectedOption.getAttribute('data-nopolisi');
        const jenis = selectedOption.getAttribute('data-jenis');
        const kapasitas = selectedOption.getAttribute('data-kapasitas');
        
        // Update image
        if (carImage) {
            if (foto) {
                carImage.src = foto;
                carImage.alt = merek;
            } else {
                carImage.src = '/images/no-car.png'; // Fallback path
                carImage.alt = 'Foto tidak tersedia';
            }
        }
        
        // Update car details
        if (carMerek) carMerek.textContent = merek || '-';
        if (carNopolisi) carNopolisi.textContent = nopolisi || '-';
        if (carKapasitas) carKapasitas.textContent = kapasitas || '-';
        
        // Update jenis badge with appropriate color
        if (carJenis) {
            carJenis.textContent = jenis || '-';
            carJenis.className = 'badge ';
            switch(jenis) {
                case 'Sedan':
                    carJenis.className += 'bg-info';
                    break;
                case 'MPV':
                    carJenis.className += 'bg-warning';
                    break;
                case 'SUV':
                    carJenis.className += 'bg-success';
                    break;
                default:
                    carJenis.className += 'bg-secondary';
            }
        }
    } else if (mobilSelect) {
        // Reset to default when no car selected
        if (carImage) {
            carImage.src = '/images/no-car.png';
            carImage.alt = 'Pilih mobil';
        }
        if (carMerek) carMerek.textContent = '-';
        if (carNopolisi) carNopolisi.textContent = '-';
        if (carJenis) {
            carJenis.textContent = '-';
            carJenis.className = 'badge bg-secondary';
        }
        if (carKapasitas) carKapasitas.textContent = '-';
    }
}

// Calculate and display return date
function calculateReturnDate() {
    const tglPesanInput = document.getElementById('tgl_pesan');
    const lamaInput = document.getElementById('lama');
    const tglKembaliDisplay = document.getElementById('tgl_kembali_display');
    
    if (tglPesanInput && lamaInput && tglKembaliDisplay) {
        if (tglPesanInput.value && lamaInput.value) {
            const tglPesan = new Date(tglPesanInput.value);
            const lama = parseInt(lamaInput.value) || 0;
            
            // Add days to the start date
            const tglKembali = new Date(tglPesan);
            tglKembali.setDate(tglKembali.getDate() + lama);
            
            // Format the date for display
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            tglKembaliDisplay.value = tglKembali.toLocaleDateString('id-ID', options);
        } else {
            tglKembaliDisplay.value = '';
        }
    }
}

// Initialize return date display on page load with existing data
function initializeReturnDateDisplay() {
    const tglPesanInput = document.getElementById('tgl_pesan');
    const lamaInput = document.getElementById('lama');
    const tglKembaliDisplay = document.getElementById('tgl_kembali_display');
    
    if (tglKembaliDisplay) {
        // Check if we have existing data
        if (tglPesanInput && lamaInput && tglPesanInput.value && lamaInput.value) {
            calculateReturnDate();
        } else {
            // If no existing data, at least show the format expected
            tglKembaliDisplay.value = '';
            tglKembaliDisplay.placeholder = 'Tanggal kembali akan ditampilkan secara otomatis';
        }
    }
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all displays first
    initializeReturnDateDisplay();
    calculateTotal();
    updateCarImage();
    
    // Set up event listeners for real-time updates
    const tglPesanInput = document.getElementById('tgl_pesan');
    const lamaInput = document.getElementById('lama');
    const mobilSelect = document.getElementById('mobil_id');
    
    // Add event listeners
    if (tglPesanInput) {
        tglPesanInput.addEventListener('change', function() {
            calculateReturnDate();
        });
    }
    
    if (lamaInput) {
        lamaInput.addEventListener('input', function() {
            calculateTotal();
            calculateReturnDate();
        });
        
        lamaInput.addEventListener('change', function() {
            calculateTotal();
            calculateReturnDate();
        });
    }
    
    if (mobilSelect) {
        mobilSelect.addEventListener('change', function() {
            calculateTotal();
            updateCarImage();
        });
    }
});

// Input validation helpers
document.addEventListener('DOMContentLoaded', function() {
    // Real-time validation feedback
    const inputs = document.querySelectorAll('#editForm input, #editForm select, #editForm textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Remove error styling when user starts typing
            this.classList.remove('is-invalid');
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Field ini wajib diisi';
        }
        
        // Specific field validations
        switch (field.id) {
            case 'lama':
                const lama = parseInt(value);
                if (value && (lama < 1 || lama > 30)) {
                    isValid = false;
                    errorMessage = 'Lama sewa harus antara 1-30 hari';
                }
                break;
                
            case 'tgl_pesan':
                if (value) {
                    const today = new Date();
                    const selectedDate = new Date(value);
                    today.setHours(0, 0, 0, 0);
                    selectedDate.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        isValid = false;
                        errorMessage = 'Tanggal tidak boleh di masa lalu';
                    }
                }
                break;
                
            case 'ponsel':
                if (value) {
                    // Basic validation for phone number or email
                    const phoneRegex = /^[0-9+\-\s()]+$/;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (!phoneRegex.test(value) && !emailRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Format nomor HP atau email tidak valid';
                    }
                }
                break;
        }
        
        // Update field styling
        if (!isValid) {
            field.classList.add('is-invalid');
            let feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = errorMessage;
            }
        } else {
            field.classList.remove('is-invalid');
        }
        
        return isValid;
    }
});