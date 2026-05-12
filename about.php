<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - FitZone Fitness Center</title>
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
        /* === About Section === */
        .about-section {
            width: 100%;
            height: 90vh;
            position: relative;
            overflow: hidden;
            background: black;
        }

        .about-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

            .about-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                opacity: 0;
                animation: fadeIn 2s ease forwards;
                filter: brightness(70%);
            }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        .about-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            padding: 20px;
            z-index: 2;
            animation: fadeInText 2s ease 0.5s forwards;
            opacity: 0;
        }

        /* Fade-in animation for text */
        @keyframes fadeInText {
            to {
                opacity: 1;
            }
        }

        .about-text h1 {
            font-size: 4rem;
            font-family: 'Anton', sans-serif;
            margin-bottom: 20px;
        }

            .about-text h1 span {
                color: maroon;
            }

        .about-text p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            line-height: 1.6;
            color: #eee;
        }

        .about-text button {
            background-color: #a40000;
            color: white;
            padding: 12px 30px;
            font-size: 1.2rem;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

            .about-text button:hover {
                background-color: #ff3333;
                transform: scale(1.05);
            }



        /* Why Choose Us Section */
        .about-why {
            text-align: center;
            padding: 60px 10px;
            background-color: #000000;
            color: #fff;
        }

            .about-why h2 {
                color: #ffffff;
                font-size: 70px;
                margin-bottom: 40px;
                font-family: 'Anton', sans-serif;
            }

            .about-why ul {
                list-style: none;
                padding: 0;
            }

                .about-why ul li {
                    background: #333;
                    margin: 15px auto;
                    padding: 20px;
                    max-width: 600px;
                    border-radius: 12px;
                    transition: all 0.3s ease;
                    font-size: 1.2rem;
                    color: #fff;
                }

                    .about-why ul li:hover {
                        background-color: #c1272d;
                        transform: translateY(-5px);
                        cursor: pointer;
                    }

        /* Meet the Team Section */
        .about-team {
            padding: 80px 50px;
            background: #000000;
            text-align: center;
        }

            .about-team h2 {
                font-size: 70px;
                color: #ffffff;
                margin-bottom: 40px;
                font-family: 'Anton', sans-serif;
            }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-member {
            background: #a40000;
            padding: 20px;
            border-radius: 15px;
            transition: transform 0.3s ease, background 0.3s ease;
        }

            .team-member:hover {
                transform: translateY(-10px);
                background: #2a2a2a;
            }

            .team-member img {
                width: 100%;
                height: 250px;
                object-fit: cover;
                border-radius: 10px;
                margin-bottom: 15px;
            }

            .team-member h3 {
                color: #fff;
                margin-bottom: 5px;
            }

            .team-member p {
                color: #ddd;
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

    <!-- About Intro Section -->
    <section class="about-section">
        <div class="about-image">
            <img src="assets/img/about-bg.png" alt="About FitZone Gym">
            <div class="about-text">
                <h1>About <span>FitZone</span></h1>
                <p>At FitZone Fitness Center, we believe fitness is a lifestyle. Our state-of-the-art gym, professional trainers, and motivating environment will push you beyond your limits. Whether you're starting your journey or an athlete aiming higher, FitZone is your ultimate destination for success.</p>
            </div>
        </div>
    </section>



    <!-- Why Choose Us Section -->
    <section class="about-why">
        <h2>Why Choose Us?</h2>
        <ul>
            <li>Certified personal trainers</li>
            <li>State-of-the-art equipment</li>
            <li>Flexible membership options</li>
            <li>Supportive and friendly environment</li>
        </ul>
    </section>

    <!-- Team Section -->
    <section class="about-team">
        <h2>Meet the Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="assets/img/trainer1.png" alt="Trainer 1">
                <h3>Amila Perera</h3>
                <p>Head Coach – Strength & Conditioning</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer2.png" alt="Trainer 2">
                <h3>Sachini Fernando</h3>
                <p>Certified Yoga & Cardio Instructor</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer3.png" alt="Trainer 3">
                <h3>Kasun Rajapaksha</h3>
                <p>Nutrition Expert</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer4.png" alt="Trainer 4">
                <h3>Dinesh Wickramasinghe</h3>
                <p>Fitness Coach – Bootcamp & HIIT</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer5.png" alt="Trainer 5">
                <h3>Aruni Jayawardena</h3>
                <p>Personal Trainer & Pilates Instructor</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer6.png" alt="Trainer 6">
                <h3>Haritha Piyumal</h3>
                <p>Group Fitness & Cardio Expert</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer7.png" alt="Trainer 7">
                <h3>Tharindu Wijesuriya</h3>
                <p>Rehabilitation & Mobility Specialist</p>
            </div>
            <div class="team-member">
                <img src="assets/img/trainer8.png" alt="Trainer 8">
                <h3>Kavinda Rajapaksha</h3>
                <p>Dance Fitness Instructor</p>
            </div>
        </div>
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
