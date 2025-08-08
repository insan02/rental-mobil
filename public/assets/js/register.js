// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    const backToTop = document.querySelector('.back-to-top');
    const registerSection = document.getElementById('register');
    const registerPosition = registerSection.offsetTop;
    
    if (window.pageYOffset > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    if (window.pageYOffset > registerPosition - 200) {
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});

// Smooth scroll to sections
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Scroll to top function
function scrollToTop() {
    document.getElementById('home').scrollIntoView({
        behavior: 'smooth'
    });
}

// Form enhancements
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });

    // Real-time validation feedback
    input.addEventListener('input', function() {
        validateField(this);
    });
});

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
            entry.target.classList.add('animate');
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.feature-card, .register-container').forEach(el => {
    observer.observe(el);
});

// Add hover effects for feature cards
document.querySelectorAll('.feature-card').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(-5px) scale(1)';
    });
});

// Add click effect for CTA buttons
document.querySelectorAll('.btn-cta').forEach(btn => {
    btn.addEventListener('click', function(e) {
        // Create ripple effect
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Form submission enhancement
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
    submitBtn.disabled = true;
    
    // Re-enable button after 5 seconds (fallback)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});

// Auto-hide alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 500);
    }, 5000);
});

// Add ripple CSS
const style = document.createElement('style');
style.textContent = `
    .btn-cta {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .focused {
        transform: scale(1.02);
    }

    /* Pulse animation for primary CTA */
    .pulse {
        animation: pulse 2s infinite;
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', function() {
    // Tunggu sebentar agar halaman fully loaded
    setTimeout(function() {
        const registerSection = document.getElementById('register');
        if (registerSection) {
            registerSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            
            // Focus pada input pertama yang error
            const firstInvalidInput = document.querySelector('.form-control.is-invalid');
            if (firstInvalidInput) {
                setTimeout(function() {
                    firstInvalidInput.focus();
                }, 800); // Delay sedikit setelah scroll selesai
            } else {
                // Jika tidak ada error, focus pada input nama
                const nameInput = document.getElementById('floatingName');
                if (nameInput) {
                    setTimeout(function() {
                        nameInput.focus();
                    }, 800);
                }
            }
        }
    }, 100);
});

document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const phoneInput = document.getElementById('floatingPhone');
    
    // Validasi nomor HP hanya angka
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Hapus karakter non-angka
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Batasi maksimal 13 digit
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }
        });
    }
    
    // Tambah loading state saat submit
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftarkan...';
                submitBtn.disabled = true;
            }
        });
    }
});