// resources/js/theme.js

class QRMenuTheme {
    constructor() {
        this.init();
    }

    init() {
        this.setupDarkModeToggle();
        this.setupCustomCursor();
        this.setupScrollEffects();
        this.setupAnimations();
        this.setupSmoothScrolling();
        this.setupFormEnhancements();
        this.setupPasswordValidation();
        this.setupEnhancedFormValidation();
        this.setupEnhancedFormSubmission();
        this.setupFloatingElements();
        this.addLoadingSpinnerCSS();
    }

    // Dark mode toggle functionality
    setupDarkModeToggle() {
        // Check for saved dark mode preference or default to system preference
        const savedTheme = localStorage.getItem('theme');
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
            document.documentElement.classList.add('dark');
        }
        
        // Add click handler to dark mode toggle (the emoji in top-right of auth card)
        document.addEventListener('click', (e) => {
            if (e.target.closest('.auth-card::before') || 
                (e.target.closest('.auth-card') && 
                 e.clientX >= e.target.closest('.auth-card').offsetWidth - 60 && 
                 e.clientY <= 60)) {
                this.toggleDarkMode();
            }
        });
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    }
    
    toggleDarkMode() {
        const isDark = document.documentElement.classList.contains('dark');
        
        if (isDark) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        
        // Add a subtle flash effect
        const flash = document.createElement('div');
        flash.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: ${isDark ? 'white' : 'black'};
            opacity: 0.1;
            z-index: 9999;
            pointer-events: none;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(flash);
        
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(flash);
            }, 300);
        }, 50);
    }

    // Custom cursor functionality
    setupCustomCursor() {
        if (window.innerWidth < 768) return; // Skip on mobile

        const cursor = document.createElement('div');
        cursor.className = 'cursor';
        cursor.style.cssText = `
            width: 20px;
            height: 20px;
            border: 2px solid var(--color-primary);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s ease;
            mix-blend-mode: difference;
            opacity: 0;
        `;
        document.body.appendChild(cursor);

        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX - 10 + 'px';
            cursor.style.top = e.clientY - 10 + 'px';
            cursor.style.opacity = '1';
        });

        document.addEventListener('mouseleave', () => {
            cursor.style.opacity = '0';
        });

        // Scale cursor on button hover
        document.querySelectorAll('.btn, button, a').forEach(element => {
            element.addEventListener('mouseenter', () => {
                cursor.style.transform = 'scale(1.5)';
            });
            element.addEventListener('mouseleave', () => {
                cursor.style.transform = 'scale(1)';
            });
        });
    }

    // Progress bar and navbar scroll effects
    setupScrollEffects() {
        // Create progress bar if it doesn't exist
        if (!document.getElementById('progress-bar')) {
            const progressBar = document.createElement('div');
            progressBar.id = 'progress-bar';
            progressBar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 3px;
                background: var(--gradient-primary);
                z-index: 9999;
                transition: width 0.1s ease;
            `;
            document.body.appendChild(progressBar);
        }

        window.addEventListener('scroll', () => {
            // Progress bar
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                progressBar.style.width = scrolled + '%';
            }

            // Navbar scroll effect
            const navbar = document.getElementById('navbar') || document.querySelector('.nav');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });
    }

    // Enhanced password validation for registration
    setupPasswordValidation() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const termsCheckbox = document.getElementById('terms');
        const submitButton = document.querySelector('button[type="submit"]');

        if (passwordInput) {
            // Password strength validation
            passwordInput.addEventListener('input', function () {
                const password = this.value;
                const requirements = document.querySelectorAll('.password-requirement');

                requirements.forEach(req => {
                    const text = req.textContent;
                    if (text.includes('8 characters') && password.length >= 8) {
                        req.classList.add('valid');
                    } else if (text.includes('8 characters')) {
                        req.classList.remove('valid');
                    }
                });

                // Update submit button state
                this.updateSubmitButtonState();
            }.bind(this));
        }

        if (confirmInput) {
            // Password confirmation validation
            confirmInput.addEventListener('input', function () {
                const password = passwordInput ? passwordInput.value : '';
                const confirm = this.value;

                // Remove any existing validation message
                const existingError = this.parentElement.querySelector('.password-mismatch');
                if (existingError) {
                    existingError.remove();
                }

                if (password !== confirm && confirm.length > 0) {
                    // Add custom error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'password-mismatch text-red-600 dark:text-red-400 text-sm mt-1';
                    errorDiv.textContent = 'Passwords do not match';
                    this.parentElement.appendChild(errorDiv);
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }

                this.updateSubmitButtonState();
            }.bind(this));
        }

        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', this.updateSubmitButtonState.bind(this));
        }
    }

    // Function to update submit button state
    updateSubmitButtonState() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const termsCheckbox = document.getElementById('terms');
        const submitButton = document.querySelector('button[type="submit"]');
        
        if (!submitButton) return;

        const password = passwordInput ? passwordInput.value : '';
        const confirm = confirmInput ? confirmInput.value : '';
        const termsAccepted = termsCheckbox ? termsCheckbox.checked : true;
        const passwordsMatch = password === confirm;
        const passwordValid = password.length >= 8;

        const isValid = passwordValid && passwordsMatch && termsAccepted && password.length > 0;

        if (isValid) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Animation intersection observer
    setupAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');

                    // Trigger counter animation for stats
                    if (entry.target.closest('.stats') || entry.target.classList.contains('counter')) {
                        this.animateCounters();
                    }
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => {
            observer.observe(el);
        });
    }

    // Smooth scrolling for anchor links
    setupSmoothScrolling() {
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
    }

    // Enhanced form validation with better UX
    setupEnhancedFormValidation() {
        // Real-time email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                // Remove existing validation message
                const existingError = this.parentElement.querySelector('.email-validation');
                if (existingError) {
                    existingError.remove();
                }

                if (email && !emailRegex.test(email)) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'email-validation text-red-600 dark:text-red-400 text-sm mt-1';
                    errorDiv.textContent = 'Please enter a valid email address';
                    this.parentElement.appendChild(errorDiv);
                }
            });

            emailInput.addEventListener('input', function() {
                // Remove error message when user starts typing
                const existingError = this.parentElement.querySelector('.email-validation');
                if (existingError) {
                    existingError.remove();
                }
            });
        }

        // Name field validation
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('blur', function() {
                const name = this.value.trim();
                
                // Remove existing validation message
                const existingError = this.parentElement.querySelector('.name-validation');
                if (existingError) {
                    existingError.remove();
                }

                if (name && name.length < 2) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'name-validation text-red-600 dark:text-red-400 text-sm mt-1';
                    errorDiv.textContent = 'Name must be at least 2 characters long';
                    this.parentElement.appendChild(errorDiv);
                }
            });
        }
    }

    // Enhanced form submission with better loading states
    setupEnhancedFormSubmission() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                
                if (submitButton && !submitButton.disabled) {
                    // Add loading state
                    submitButton.style.opacity = '0.8';
                    submitButton.disabled = true;
                    submitButton.classList.add('relative');
                    
                    // Store original text
                    const originalText = submitButton.textContent;
                    const isRegisterForm = form.action.includes('register');
                    const loadingText = isRegisterForm ? 'Creating Account...' : 'Signing In...';
                    
                    submitButton.textContent = loadingText;
                    
                    // Add loading spinner
                    const spinner = document.createElement('div');
                    spinner.className = 'loading-spinner';
                    spinner.style.cssText = `
                        position: absolute;
                        right: 1rem;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 1rem;
                        height: 1rem;
                        border: 2px solid rgba(255, 255, 255, 0.3);
                        border-top: 2px solid white;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    `;
                    
                    submitButton.appendChild(spinner);
                    
                    // Reset after delay (in case of validation errors)
                    setTimeout(() => {
                        if (submitButton.querySelector('.loading-spinner')) {
                            submitButton.style.opacity = '1';
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                            const existingSpinner = submitButton.querySelector('.loading-spinner');
                            if (existingSpinner) {
                                existingSpinner.remove();
                            }
                        }
                    }, 5000);
                }
            });
        });
    }

    // Add CSS for the loading spinner animation
    addLoadingSpinnerCSS() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: translateY(-50%) rotate(0deg); }
                100% { transform: translateY(-50%) rotate(360deg); }
            }
            
            .loading-spinner {
                pointer-events: none;
            }
        `;
        document.head.appendChild(style);
    }

    // Enhanced form functionality (minimal to avoid conflicts)
    setupFormEnhancements() {
        // Only add basic form validation enhancement that doesn't interfere with Laravel components
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitButton && !submitButton.disabled) {
                    submitButton.style.opacity = '0.7';
                    submitButton.disabled = true;
                    
                    // Add loading state
                    const originalText = submitButton.textContent;
                    submitButton.textContent = 'Processing...';
                    
                    // Reset after delay (in case of client-side validation errors)
                    setTimeout(() => {
                        submitButton.style.opacity = '1';
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }, 3000);
                }
            });
        });
    }

    // Counter animations
    animateCounters() {
        const counters = document.querySelectorAll('.counter:not(.animated)');

        counters.forEach(counter => {
            counter.classList.add('animated');
            const target = +counter.getAttribute('data-target') || +counter.textContent.replace(/[^\d.]/g, '');
            const isDecimal = target.toString().includes('.');
            let current = 0;
            const increment = target / 100;
            const duration = 2000; // 2 seconds
            const stepTime = duration / 100;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }

                counter.textContent = isDecimal ? current.toFixed(1) : Math.floor(current);
            }, stepTime);
        });
    }

    // Create floating background elements
    setupFloatingElements() {
        const hero = document.querySelector('.hero');
        if (!hero) return;

        const container = document.createElement('div');
        container.className = 'floating-elements';
        container.style.cssText = `
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        `;
        hero.appendChild(container);

        for (let i = 0; i < 15; i++) {
            const element = document.createElement('div');
            element.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: var(--color-primary);
                border-radius: 50%;
                opacity: 0.6;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 10 + 20}s ease-in-out infinite,
                          pulse ${Math.random() * 3 + 2}s ease-in-out infinite;
            `;
            container.appendChild(element);
        }
    }

    // Utility methods
    static showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: var(--color-dark-lighter);
            color: var(--color-white);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--color-${type === 'error' ? 'red' : type === 'success' ? 'green' : 'primary'});
            box-shadow: var(--shadow-card);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    static addLoadingState(element) {
        element.style.opacity = '0.7';
        element.style.pointerEvents = 'none';
        const originalText = element.textContent;
        element.textContent = 'Loading...';

        return () => {
            element.style.opacity = '1';
            element.style.pointerEvents = 'auto';
            element.textContent = originalText;
        };
    }
}



// CSS Animations (to be added to CSS file)
const animationCSS = `
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(180deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
`;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.qrMenuTheme = new QRMenuTheme();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QRMenuTheme;
}