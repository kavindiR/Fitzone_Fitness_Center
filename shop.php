<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop - FitZone</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <header class="main-header">
        <a href="#home" class="logo">Fitzone <span>Fitness</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="membership.php">Membership</a>
            <a href="classes.php">Classes</a>
            <a href="blog.php">Blogs</a>
            <a href="contact.php">Contact</a>
            <a href="shop.php">Shop</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn-login">Login</a>
            <?php else: ?>
            <a href="logout.php" class="btn-logout">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <style>
        .shop-intro {
            padding: 80px 40px;
            text-align: center;
            background-color: #000000;
        }

            .shop-intro h1 {
                font-size: 3.5rem;
                color: var(--main-color);
                margin-bottom: 20px;
                animation: fadeInUp 1.5s ease-out;
            }

            .shop-intro p {
                font-size: 1.2rem;
                color: #ddd;
                animation: fadeInUp 1.8s ease-out;
            }

        /* Product Grid Section */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px;
            background-color: #222;
            border-radius: 10px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card {
            background-color: #333;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

            .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .product-card img {
                max-width: 100%;
                border-radius: 8px;
                margin-bottom: 15px;
                transition: transform 0.3s ease-in-out;
            }

                .product-card img:hover {
                    transform: scale(1.05);
                }

            .product-card h3 {
                font-size: 1.6rem;
                color: var(--accent-color);
                margin-bottom: 10px;
            }

            .product-card p {
                font-size: 1.2rem;
                color: var(--text-color);
                margin-bottom: 15px;
            }

            .product-card button {
                background-color: var(--main-color);
                color: #fff;
                font-size: 1.1rem;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.3s ease;
            }

                .product-card button:hover {
                    background-color: var(--secondary-color);
                    transform: scale(1.05);
                }

        /* Shop Policies Section */
        .shop-policies {
            padding: 60px 40px;
            background-color: #000000;
            text-align: center;
        }

            .shop-policies h2 {
                font-size: 2rem;
                color: var(--main-color);
                margin-bottom: 15px;
                animation: fadeInUp 1.8s ease-out;
            }

            .shop-policies p {
                font-size: 1.1rem;
                color: #ddd;
                margin-bottom: 30px;
                animation: fadeInUp 2s ease-out;
            }
                   .popular {
           background: linear-gradient(145deg, #1f1f1f, #2c2c2c);
           border: 2px solid maroon;
           transform: scale(1.05);
           animation: pulsePop 2s infinite ease-in-out;
       }
       .site-footer {
       background-color: #000;
       color: #fff;
       padding: 60px 0 0;
       font-family: 'Roboto', sans-serif;
       border-top: 2px solid #a40000;
   }

   .footer-container {
       max-width: 1200px;
       margin: 0 auto;
       padding: 0 20px;
   }

   .footer-top {
       display: grid;
       grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
       gap: 40px;
       margin-bottom: 50px;
   }

   .footer-column h3 {
       font-size: 1.5rem;
       color: #a40000;
       margin-bottom: 25px;
       font-family: 'Anton', sans-serif;
       letter-spacing: 1px;
       position: relative;
   }

   .footer-column h3::after {
       content: '';
       position: absolute;
       left: 0;
       bottom: -10px;
       width: 50px;
       height: 3px;
       background-color: #a40000;
   }

   .footer-logo {
       font-family: 'Anton', sans-serif;
       font-size: 2rem;
       color: #fff;
       margin-top: 20px;
   }

   .footer-logo span {
       color: #a40000;
   }

   .footer-column p {
       color: #ccc;
       line-height: 1.6;
       margin-bottom: 20px;
   }

   .footer-column ul {
       list-style: none;
       padding: 0;
   }

   .footer-column ul li {
       margin-bottom: 12px;
   }

   .footer-column ul li a {
       color: #ccc;
       text-decoration: none;
       transition: color 0.3s, padding-left 0.3s;
   }

   .footer-column ul li a:hover {
       color: #a40000;
       padding-left: 5px;
   }

   .contact-info li {
       display: flex;
       align-items: center;
       margin-bottom: 15px;
       color: #ccc;
   }

   .contact-info i {
       color: #a40000;
       margin-right: 10px;
       width: 20px;
       text-align: center;
   }

   .social-links {
       display: flex;
       gap: 15px;
       margin-bottom: 30px;
   }

   .social-links a {
       display: flex;
       align-items: center;
       justify-content: center;
       width: 40px;
       height: 40px;
       background-color: #222;
       color: #fff;
       border-radius: 50%;
       transition: all 0.3s;
   }

   .social-links a:hover {
       background-color: #a40000;
       transform: translateY(-3px);
   }

   .newsletter-title {
       margin-top: 30px;
   }

   .newsletter-form {
       display: flex;
       margin-top: 15px;
   }

   .newsletter-form input {
       flex: 1;
       padding: 12px 15px;
       border: none;
       border-radius: 4px 0 0 4px;
       font-size: 14px;
   }

   .newsletter-form button {
       background-color: #a40000;
       color: white;
       border: none;
       padding: 0 20px;
       border-radius: 0 4px 4px 0;
       cursor: pointer;
       transition: background-color 0.3s;
   }

   .newsletter-form button:hover {
       background-color: #c1272d;
   }

   .footer-bottom {
       border-top: 1px solid #333;
       padding: 25px 0;
       display: flex;
       flex-wrap: wrap;
       justify-content: space-between;
       align-items: center;
   }

   .copyright {
       color: #999;
       font-size: 14px;
   }

   .footer-links {
       display: flex;
       gap: 20px;
   }

   .footer-links a {
       color: #999;
       text-decoration: none;
       font-size: 14px;
       transition: color 0.3s;
   }

   .footer-links a:hover {
       color: #a40000;
   }

   /* Responsive Design */
   @media (max-width: 768px) {
       .footer-top {
           grid-template-columns: 1fr;
       }
       
       .footer-bottom {
           flex-direction: column;
           gap: 15px;
           text-align: center;
       }
       
       .footer-links {
           justify-content: center;
       }
   }
    </style>

    <section class="shop-intro">
        <h1>FitZone Shop</h1>
        <p>Buy top-quality fitness gear, apparel, and supplements.</p>
    </section>

    <section class="product-grid">
        <div class="product-card">
            <img src="assets/img/shaker.png" alt="Protein Shaker" />
            <h3>Protein Shaker</h3>
            <p>Rs. 1,200</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="assets/img/gloves.png" alt="Gym Gloves" />
            <h3>Gym Gloves</h3>
            <p>Rs. 1,800</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="assets/img/tshirt.png" alt="FitZone T-Shirt" />
            <h3>FitZone T-Shirt</h3>
            <p>Rs. 2,500</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="assets/img/whey.png" alt="Whey Protein 1kg" />
            <h3>Whey Protein (2.27kg)</h3>
            <p>Rs. 6,900</p>
            <button>Add to Cart</button>
        </div>
    </section>

    <section class="shop-policies">
        <h2>Return Policy</h2>
        <p>Items can be returned within 7 days of purchase with a valid receipt. Opened supplements and worn apparel are not eligible for return.</p>

        <h2>Shipping Policy</h2>
        <p>We deliver island-wide within 3-5 working days. Free delivery for orders over Rs. 5,000. Tracking will be shared via SMS/email once dispatched.</p>
    </section>

    <!-- Footer -->
<footer class="site-footer">
    <div class="footer-container">
        <!-- Footer Top Section -->
        <div class="footer-top">
            <div class="footer-column">
                <h3>About FitZone</h3>
                <p>Your premier fitness destination offering state-of-the-art facilities, expert trainers, and a variety of classes to help you achieve your fitness goals.</p>
                <div class="footer-logo">FitZone <span>Fitness</span></div>
            </div>

            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="membership.php">Membership</a></li>
                    <li><a href="classes.php">Classes</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Contact Info</h3>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, Colombo, Sri Lanka</li>
                    <li><i class="fas fa-phone"></i> +94 76 123 4567</li>
                    <li><i class="fas fa-envelope"></i> info@fitzone.com</li>
                    <li><i class="fas fa-clock"></i> Open Daily: 6:00 AM - 10:00 PM</li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/fitzone" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://instagram.com/fitzone" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://youtube.com/fitzone" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="https://wa.me/94761234567" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                
                <h3 class="newsletter-title">Newsletter</h3>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your Email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; 2025 FitZone Fitness Center. All Rights Reserved.
            </div>
            <div class="footer-links">
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms of Service</a>
                <a href="sitemap.php">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
   

</body>
</html>
