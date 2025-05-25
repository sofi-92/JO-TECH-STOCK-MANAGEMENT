<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster Pro | Intelligent Inventory Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e6f0ff;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #fd7e14;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
            --white: #ffffff;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fbfd;
            color: #4a5568;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: var(--gradient);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
        }

        .logo i {
            margin-right: 10px;
            font-size: 28px;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 30px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: 4px;
        }

        nav ul li a:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .cta-button {
            background-color: white;
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            transition: var(--transition);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* Hero Section */
        .hero {
            padding: 180px 0 100px;
            background: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            position: relative;
            color: white;
            text-align: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }

        .hero-buttons .btn {
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
        }

        .btn-primary:hover, .btn-outline:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background-color: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .section-title p {
            color: #718096;
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background-color: var(--light);
            border-radius: 10px;
            padding: 30px;
            transition: var(--transition);
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: var(--primary);
            font-size: 32px;
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        /* Dashboard Preview */
        .dashboard-preview {
            padding: 100px 0;
            background-color: #f5f7fb;
        }

        .dashboard-image {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-top: 40px;
            transition: var(--transition);
        }

        .dashboard-image:hover {
            transform: scale(1.02);
        }

        /* Testimonials */
        .testimonials {
            padding: 100px 0;
            background-color: white;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .testimonial-card {
            background-color: var(--light);
            border-radius: 10px;
            padding: 30px;
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 60px;
            color: var(--primary);
            opacity: 0.1;
            font-family: serif;
        }

        .testimonial-content {
            margin-bottom: 20px;
            font-style: italic;
            color: #4a5568;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-weight: bold;
        }

        .author-info h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .author-info p {
            color: #718096;
            font-size: 14px;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: var(--gradient);
            color: white;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta-section p {
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #a0aec0;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-column ul li a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: var(--transition);
        }

        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #a0aec0;
            font-size: 14px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                margin-top: 20px;
            }

            nav ul li {
                margin: 0 10px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 18px;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .section-title h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <i class="fas fa-boxes"></i>
                <span>StockMaster Pro</span>
            </div>
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#" class="cta-button">Get Started</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Revolutionize Your Inventory Management</h1>
            <p>StockMaster Pro delivers powerful, intuitive stock control with real-time analytics to help you optimize your inventory and boost profitability.</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Start Free Trial</a>
                <a href="#" class="btn btn-outline">Watch Demo</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Inventory Features</h2>
                <p>Everything you need to take complete control of your stock levels, orders, and supply chain</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Real-Time Analytics</h3>
                    <p>Get instant insights into your inventory performance with beautiful, interactive dashboards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <h3>Barcode Scanning</h3>
                    <p>Quickly add or remove items with our lightning-fast barcode scanning technology.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Smart Alerts</h3>
                    <p>Automated notifications when stock levels are low or items are approaching expiration.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Access</h3>
                    <p>Manage your inventory from anywhere with our fully responsive mobile interface.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3>Multi-Location</h3>
                    <p>Track inventory across multiple warehouses or stores from a single dashboard.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3>Advanced Reporting</h3>
                    <p>Generate detailed reports and export data in multiple formats for analysis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview -->
    <section class="dashboard-preview" id="dashboard">
        <div class="container">
            <div class="section-title">
                <h2>Beautiful, Intuitive Dashboard</h2>
                <p>Our clean, modern interface makes inventory management simple and enjoyable</p>
            </div>
            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80" alt="StockMaster Pro Dashboard" class="dashboard-image">
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>Trusted by Businesses Worldwide</h2>
                <p>Don't just take our word for it - hear what our customers have to say</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        StockMaster Pro has transformed how we manage our inventory. We've reduced stockouts by 80% and improved our inventory turnover ratio significantly.
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">JD</div>
                        <div class="author-info">
                            <h4>John D.</h4>
                            <p>Retail Chain Owner</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        The mobile access has been a game-changer for our warehouse team. We can now update stock levels in real-time from anywhere in the facility.
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SM</div>
                        <div class="author-info">
                            <h4>Sarah M.</h4>
                            <p>Operations Manager</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        As a small business owner, I needed an affordable solution that didn't compromise on features. StockMaster Pro delivered exactly what I needed.
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">AR</div>
                        <div class="author-info">
                            <h4>Alex R.</h4>
                            <p>Boutique Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Transform Your Inventory Management?</h2>
            <p>Join thousands of businesses that are already optimizing their stock with StockMaster Pro</p>
            <a href="#" class="btn btn-primary">Start Your Free 14-Day Trial</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>StockMaster Pro</h3>
                    <p>The most powerful, intuitive inventory management solution for businesses of all sizes.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Product</h3>
                    <ul>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Integrations</a></li>
                        <li><a href="#">Updates</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Resources</h3>
                    <ul>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Tutorials</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">API</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; 2023 StockMaster Pro. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>