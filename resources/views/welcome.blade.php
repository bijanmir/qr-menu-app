<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Transform your restaurant with smart digital menus. Increase sales, reduce costs, and delight customers with our premium QR menu solution.">
        <meta name="keywords" content="digital menu, QR menu, restaurant technology, contactless dining, menu management">
        <meta name="author" content="QR Menu Pro">
        
        <!-- Open Graph -->
        <meta property="og:title" content="QR Menu Pro - Transform Your Restaurant Experience">
        <meta property="og:description" content="Smart digital menus that increase sales and delight customers">
        <meta property="og:type" content="website">
        <meta property="og:image" content="/og-image.jpg">
        
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="QR Menu Pro - Transform Your Restaurant Experience">
        <meta name="twitter:description" content="Smart digital menus that increase sales and delight customers">
        
        <title>QR Menu Pro - Transform Your Restaurant Experience</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Styles -->
        <style>
            :root {
                --color-primary: #00d4aa;
                --color-primary-dark: #00b897;
                --color-dark: #0a0a0b;
                --color-dark-light: #1a1a1c;
                --color-dark-lighter: #2a2a2e;
                --color-gray: #9ca3af;
                --color-gray-light: #d1d5db;
                --color-white: #ffffff;
                --gradient-primary: linear-gradient(135deg, #00d4aa 0%, #00b897 100%);
                --gradient-dark: linear-gradient(135deg, #0a0a0b 0%, #1a1a1c 100%);
                --shadow-primary: 0 4px 20px rgba(0, 212, 170, 0.3);
                --shadow-dark: 0 4px 20px rgba(0, 0, 0, 0.5);
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                line-height: 1.6;
                color: var(--color-white);
                background: var(--color-dark);
                overflow-x: hidden;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
            }
            
            /* Custom Cursor */
            .cursor {
                width: 20px;
                height: 20px;
                border: 2px solid var(--color-primary);
                border-radius: 50%;
                position: fixed;
                pointer-events: none;
                z-index: 9999;
                transition: transform 0.1s ease;
                mix-blend-mode: difference;
            }
            
            /* Hero Section */
            .hero {
                min-height: 100vh;
                background: var(--gradient-dark);
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            
            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2300d4aa' fill-opacity='0.05'%3E%3Cpath d='M0 40L40 0h20L20 40 0 80z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                animation: float 30s ease-in-out infinite;
            }
            
            .floating-elements {
                position: absolute;
                width: 100%;
                height: 100%;
                pointer-events: none;
            }
            
            .floating-element {
                position: absolute;
                width: 4px;
                height: 4px;
                background: var(--color-primary);
                border-radius: 50%;
                opacity: 0.6;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-30px) rotate(180deg); }
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 0.6; }
                50% { opacity: 1; }
            }
            
            .hero-content {
                position: relative;
                z-index: 2;
                text-align: center;
                animation: fadeInUp 1s ease-out;
            }
            
            .hero h1 {
                font-size: clamp(2.5rem, 5vw, 4.5rem);
                font-weight: 700;
                margin-bottom: 1.5rem;
                line-height: 1.1;
                background: linear-gradient(135deg, var(--color-white) 0%, var(--color-primary) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .hero p {
                font-size: 1.25rem;
                margin-bottom: 2.5rem;
                color: var(--color-gray);
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
                font-weight: 300;
            }
            
            /* Navigation */
            nav {
                position: fixed;
                top: 0;
                width: 100%;
                background: rgba(10, 10, 11, 0.85);
                backdrop-filter: blur(20px);
                z-index: 1000;
                padding: 1rem 0;
                transition: all 0.3s ease;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            nav.scrolled {
                background: rgba(10, 10, 11, 0.95);
                box-shadow: var(--shadow-dark);
            }
            
            .nav-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .logo {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--color-primary);
                text-decoration: none;
                transition: all 0.3s ease;
            }
            
            .logo:hover {
                text-shadow: 0 0 10px var(--color-primary);
            }
            
            .nav-links {
                display: flex;
                gap: 2rem;
                align-items: center;
            }
            
            .nav-links a {
                text-decoration: none;
                color: var(--color-gray);
                font-weight: 500;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .nav-links a:hover {
                color: var(--color-primary);
            }
            
            .nav-links a::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: -5px;
                left: 0;
                background: var(--color-primary);
                transition: width 0.3s ease;
            }
            
            .nav-links a:hover::after {
                width: 100%;
            }
            
            /* Buttons */
            .btn {
                display: inline-block;
                padding: 1rem 2rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                font-family: inherit;
                position: relative;
                overflow: hidden;
            }
            
            .btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
                transition: left 0.6s;
            }
            
            .btn:hover::before {
                left: 100%;
            }
            
            .btn-primary {
                background: var(--gradient-primary);
                color: var(--color-dark);
                box-shadow: var(--shadow-primary);
                font-weight: 700;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 30px rgba(0, 212, 170, 0.4);
            }
            
            .btn-secondary {
                background: transparent;
                color: var(--color-gray);
                border: 2px solid var(--color-dark-lighter);
            }
            
            .btn-secondary:hover {
                border-color: var(--color-primary);
                color: var(--color-primary);
                box-shadow: 0 0 20px rgba(0, 212, 170, 0.2);
            }
            
            .btn-outline {
                background: transparent;
                color: var(--color-primary);
                border: 2px solid var(--color-primary);
            }
            
            .btn-outline:hover {
                background: var(--color-primary);
                color: var(--color-dark);
                transform: translateY(-2px);
            }
            
            /* Features Section */
            .features {
                padding: 8rem 0;
                background: var(--color-dark-light);
                position: relative;
            }
            
            .features::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, var(--color-primary), transparent);
            }
            
            .features h2 {
                text-align: center;
                font-size: 3rem;
                margin-bottom: 3rem;
                color: var(--color-white);
                font-weight: 700;
                background: linear-gradient(135deg, var(--color-white) 0%, var(--color-primary) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 2rem;
                margin-top: 4rem;
            }
            
            .feature-card {
                text-align: center;
                padding: 3rem 2rem;
                border-radius: 20px;
                background: var(--color-dark-lighter);
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: all 0.4s ease;
                position: relative;
                overflow: hidden;
            }
            
            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, var(--color-primary) 0%, transparent 70%);
                opacity: 0;
                transition: opacity 0.4s ease;
            }
            
            .feature-card:hover::before {
                opacity: 0.1;
            }
            
            .feature-card:hover {
                transform: translateY(-10px);
                border-color: var(--color-primary);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }
            
            .feature-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto 2rem;
                background: var(--gradient-primary);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                color: var(--color-dark);
                position: relative;
                z-index: 2;
                transition: transform 0.3s ease;
            }
            
            .feature-card:hover .feature-icon {
                transform: scale(1.1);
            }
            
            .feature-card h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: var(--color-white);
                font-weight: 600;
                position: relative;
                z-index: 2;
            }
            
            .feature-card p {
                color: var(--color-gray);
                line-height: 1.8;
                position: relative;
                z-index: 2;
            }
            
            /* Stats Section */
            .stats {
                padding: 8rem 0;
                background: var(--color-dark);
                position: relative;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 3rem;
                text-align: center;
            }
            
            .stat {
                position: relative;
            }
            
            .stat h3 {
                font-size: 4rem;
                font-weight: 700;
                color: var(--color-primary);
                margin-bottom: 0.5rem;
                line-height: 1;
            }
            
            .stat p {
                color: var(--color-gray);
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-size: 0.9rem;
            }
            
            .counter {
                display: inline-block;
            }
            
            /* CTA Section */
            .cta {
                padding: 8rem 0;
                background: var(--color-dark-light);
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .cta::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, var(--color-primary) 0%, transparent 70%);
                opacity: 0.05;
                animation: rotate 20s linear infinite;
            }
            
            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .cta h2 {
                font-size: 3rem;
                margin-bottom: 1rem;
                color: var(--color-white);
                font-weight: 700;
                position: relative;
                z-index: 2;
            }
            
            .cta p {
                font-size: 1.25rem;
                margin-bottom: 3rem;
                color: var(--color-gray);
                position: relative;
                z-index: 2;
                font-weight: 300;
            }
            
            /* Footer */
            footer {
                background: var(--color-dark);
                color: var(--color-gray);
                text-align: center;
                padding: 3rem 0;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            /* Mobile Styles */
            @media (max-width: 768px) {
                .nav-links {
                    display: none;
                }
                
                .hero h1 {
                    font-size: 2.5rem;
                }
                
                .hero p {
                    font-size: 1.1rem;
                }
                
                .container {
                    padding: 0 1rem;
                }
                
                .features h2,
                .cta h2 {
                    font-size: 2rem;
                }
                
                .stat h3 {
                    font-size: 2.5rem;
                }
                
                .features,
                .stats,
                .cta {
                    padding: 4rem 0;
                }
            }
            
            /* Animations */
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
            
            .fade-in {
                opacity: 0;
                transform: translateY(50px);
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            .fade-in.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .slide-in-left {
                opacity: 0;
                transform: translateX(-50px);
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            .slide-in-left.visible {
                opacity: 1;
                transform: translateX(0);
            }
            
            .slide-in-right {
                opacity: 0;
                transform: translateX(50px);
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            .slide-in-right.visible {
                opacity: 1;
                transform: translateX(0);
            }
            
            /* Scroll Progress Bar */
            .progress-bar {
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 3px;
                background: var(--gradient-primary);
                z-index: 9999;
                transition: width 0.1s ease;
            }
        </style>
    </head>
    <body>
        <!-- Progress Bar -->
        <div class="progress-bar" id="progress-bar"></div>
        
        <!-- Custom Cursor -->
        <div class="cursor" id="cursor"></div>

        <!-- Navigation -->
        <nav id="navbar">
            <div class="container nav-content">
                <a href="/" class="logo">QR Menu Pro</a>
                <div class="nav-links">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}">Sign In</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="floating-elements">
                <!-- Floating elements will be generated by JavaScript -->
            </div>
            <div class="container">
                <div class="hero-content">
                    <h1>Transform Your Restaurant Experience</h1>
                    <p>Elevate dining with smart digital menus that increase sales, reduce costs, and create unforgettable customer experiences.</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Start Free Trial</a>
                        @endif
                        <a href="#features" class="btn btn-secondary">Learn More</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <div class="container">
                <h2 class="fade-in">Why Choose QR Menu Pro?</h2>
                <div class="features-grid">
                    <div class="feature-card slide-in-left">
                        <div class="feature-icon">📱</div>
                        <h3>Instant Digital Menus</h3>
                        <p>Transform your paper menus into beautiful, interactive digital experiences that customers can access instantly with their phones.</p>
                    </div>
                    <div class="feature-card fade-in">
                        <div class="feature-icon">📈</div>
                        <h3>Boost Sales</h3>
                        <p>Smart upselling recommendations and engaging visuals help increase average order value and customer satisfaction.</p>
                    </div>
                    <div class="feature-card slide-in-right">
                        <div class="feature-icon">⚡</div>
                        <h3>Real-time Updates</h3>
                        <p>Update prices, availability, and menu items instantly across all locations without reprinting a single menu.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat fade-in">
                        <h3><span class="counter" data-target="35">0</span>%</h3>
                        <p>Average Sales Increase</p>
                    </div>
                    <div class="stat fade-in">
                        <h3><span class="counter" data-target="2500">0</span>+</h3>
                        <p>Restaurants Trust Us</p>
                    </div>
                    <div class="stat fade-in">
                        <h3><span class="counter" data-target="60">0</span>%</h3>
                        <p>Reduced Menu Costs</p>
                    </div>
                    <div class="stat fade-in">
                        <h3><span class="counter" data-target="4.9">0</span>★</h3>
                        <p>Customer Rating</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2 class="fade-in">Ready to Transform Your Restaurant?</h2>
                <p class="fade-in">Join thousands of restaurants already using QR Menu Pro to delight customers and increase revenue.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary fade-in">Start Your Free Trial Today</a>
                @endif
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <p>&copy; 2024 QR Menu Pro. All rights reserved.</p>
            </div>
        </footer>

        <!-- JavaScript -->
        <script>
            // Custom cursor
            const cursor = document.getElementById('cursor');
            document.addEventListener('mousemove', (e) => {
                cursor.style.left = e.clientX - 10 + 'px';
                cursor.style.top = e.clientY - 10 + 'px';
            });

            // Hide cursor when leaving window
            document.addEventListener('mouseleave', () => {
                cursor.style.opacity = '0';
            });
            document.addEventListener('mouseenter', () => {
                cursor.style.opacity = '1';
            });

            // Progress bar
            window.addEventListener('scroll', () => {
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (winScroll / height) * 100;
                document.getElementById('progress-bar').style.width = scrolled + '%';
            });

            // Navbar scroll effect
            window.addEventListener('scroll', () => {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Smooth scrolling for anchor links
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

            // Generate floating elements
            function createFloatingElements() {
                const container = document.querySelector('.floating-elements');
                for (let i = 0; i < 20; i++) {
                    const element = document.createElement('div');
                    element.classList.add('floating-element');
                    element.style.left = Math.random() * 100 + '%';
                    element.style.top = Math.random() * 100 + '%';
                    element.style.animationDelay = Math.random() * 20 + 's';
                    element.style.animationDuration = (Math.random() * 10 + 20) + 's';
                    element.style.animation = `float ${element.style.animationDuration} ease-in-out infinite, pulse ${Math.random() * 3 + 2}s ease-in-out infinite`;
                    container.appendChild(element);
                }
            }

            // Counter animation
            function animateCounters() {
                const counters = document.querySelectorAll('.counter');
                
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const increment = target / 200;
                    
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(() => animateCounters(), 10);
                    } else {
                        counter.innerText = target;
                    }
                });
            }

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        
                        // Trigger counter animation when stats section is visible
                        if (entry.target.closest('.stats')) {
                            setTimeout(animateCounters, 300);
                        }
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => {
                observer.observe(el);
            });

            // Initialize
            document.addEventListener('DOMContentLoaded', () => {
                createFloatingElements();
            });

            // Add hover effects to buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('mouseenter', () => {
                    cursor.style.transform = 'scale(1.5)';
                });
                btn.addEventListener('mouseleave', () => {
                    cursor.style.transform = 'scale(1)';
                });
            });
        </script>
    </body>
</html>