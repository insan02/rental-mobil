// Hide spinner when page loads
window.addEventListener('load', function() {
    document.getElementById('spinner').style.display = 'none';
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    const backToTop = document.querySelector('.back-to-top');
    const loginSection = document.getElementById('login');
    const loginPosition = loginSection.offsetTop;
    
    if (window.pageYOffset > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    if (window.pageYOffset > loginPosition - 200) {
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
document.querySelectorAll('.feature-card, .login-container, .car-item, .stat-item').forEach(el => {
    observer.observe(el);
});

// Add hover effects for car items
document.querySelectorAll('.car-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.05)';
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
`;
document.head.appendChild(style);

// Enhanced loading states
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            submitBtn.disabled = true;
        }
    });
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

document.addEventListener('DOMContentLoaded', function() {
            // Tunggu sebentar agar halaman fully loaded
            setTimeout(function() {
                const loginSection = document.getElementById('login');
                if (loginSection) {
                    loginSection.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Optional: Focus pada input email yang error
                    const emailInput = document.getElementById('floatingInput');
                    if (emailInput && emailInput.classList.contains('is-invalid')) {
                        setTimeout(function() {
                            emailInput.focus();
                        }, 800); // Delay sedikit setelah scroll selesai
                    }
                }
            }, 100);
        });