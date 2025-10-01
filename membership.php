<?php
// Start the session (if needed for user authentication)
session_start();

// Set the page title
$pageTitle = "Membership Plans - FitZone";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Membership Plans - FitZone</title>
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
/* === Membership Intro Section === */
.membership-intro {
    width: 100%;
    height: 90vh; /* Full viewport height */
    position: relative;
    overflow: hidden;
    background: black;
}

.membership-image {
    position: absolute; /* Ensure it stretches and fits within the section */
    top: 0;
    left: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
}

        .membership-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Make sure the image covers the entire section */
            display: block;
            opacity: 0;
            animation: fadeIn 2s ease forwards;
            filter: brightness(60%); /* Darken the image */
        }

/* Fade-in animation for image */
@keyframes fadeIn {
    to {
        opacity: 1;
    }
}

.membership-text {
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

.membership-text h1 {
    font-size: 4rem;
    font-family: 'Anton', sans-serif;
    margin-bottom: 20px;
}

    .membership-text h1 span {
        color: #a40000; /* Maroon color */
    }

.membership-text p {
    font-size: 1.5rem;
    margin-bottom: 30px;
    line-height: 1.6;
    color: #eee;
}

.membership-text .btn {
    background-color: #a40000;
    color: white;
    padding: 12px 30px;
    font-size: 1.2rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

    .membership-text .btn:hover {
        background-color: #ff3333;
        transform: scale(1.05);
    }



/* === Membership Plans Section === */
.membership-plans {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    padding: 100px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

        .plan {
            background: #1f1f1f;
            padding: 70px 50px;
            border-radius: 50px;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.7);
        }

        .plan-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px; /* Center the icon and add spacing */

        }

            .plan-icon img {
                width: 100%;
                height: 100%;
                object-fit: contain; /* So the image looks good inside */
                transition: transform 0.4s ease;
                border-radius: 50px;
            }

        .plan:hover .plan-icon img {
            transform: scale(1.1);
        }


    .plan:hover {
        transform: translateY(-12px);
        background: #292929;
        box-shadow: 0 12px 25px rgba(0,0,0,0.8);
    }

    .plan h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        font-family: 'Anton', sans-serif;
        color: #fff;
    }

    .plan .price {
        font-size: 2rem;
        color: #c1272d;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .plan ul {
        margin-bottom: 30px;
    }

        .plan ul li {
            margin: 12px 0;
            font-size: 1.2rem;
            color: #ccc;
        }

    /* Highlight Popular Plan */
    .plan.popular {
        border: 3px solid #c1272d;
        background: #262626;
    }

/* === Membership Benefits Section === */
.membership-benefits {
    padding: 100px 20px;
    text-align: center;
    background:#000000; /* Light gray background for contrast */
    position: relative;
}

    .membership-benefits::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.1); /* Subtle overlay */
        z-index: -1;
    }

    .membership-benefits h2 {
        font-family: 'Anton', sans-serif;
        font-size: 3.8rem;
        margin-bottom: 40px;
        color: #a40000; /* Maroon color */
        animation: fadeInDown 1.2s ease forwards;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

.benefits-list {
    max-width: 700px;
    margin: 0 auto;
    list-style: none;
    padding: 0;
}

    .benefits-list li {
        font-size: 1.6rem;
        color:#ffffff; /* Darker text for better readability */
        margin: 20px 0;
        position: relative;
        padding-left: 0; /* Removed left padding */
        line-height: 1.8;
        animation: fadeInUp 1.4s ease forwards;
        transition: color 0.3s ease, transform 0.3s ease;
    }

        .benefits-list li:hover {
            color: #c1272d; /* Highlight text on hover */
            transform: scale(1.05); /* Slight scale effect */
            cursor: pointer; /* Indicating interactiveness */
        }

            /* Hover effect on the list items */
            .benefits-list li:hover::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: -5px;
                width: 100%;
                height: 2px;
                background-color: #a40000; /* Red underline effect */
                animation: underline 0.3s ease forwards;
            }

/* Red underline animation for hover effect */
@keyframes underline {
    0% {
        width: 0;
    }

    100% {
        width: 100%;
    }
}

/* Button Styles */
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background: #c1272d;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
        }

            .btn:hover {
                background: #a51f25;
                transform: scale(1.05);
                box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
                text-decoration: none;
            }

/* Animations */
@keyframes fadeInDown {
    0% {
        opacity: 0;
        transform: translateY(-50px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
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

    <!-- Membership Intro Section -->
    <section class="membership-intro">
        <div class="membership-image">
            <img src="assets/img/membership-bg.png" alt="Membership Background">
        </div>
        <div class="membership-text">
            <h1>Choose Your Plan <span>Today</span></h1>
            <p>Flexible and affordable membership options for everyone.</p>
            <a href="membership_register.php" class="btn">Join Specific Class</a>
        </div>
    </section>


    <!-- Membership Plans Section -->
<section id="membership-plans" class="membership-plans">
    <div class="plan">
        <div class="plan-icon">
            <img src="assets/img/basic-plan-icon.png" alt="Basic Plan Icon" />
        </div>
        <h2>Basic Plan</h2>
        <p class="price">Rs. 3,000 / month</p>
        <ul>
            <li>Gym Access (8AM - 6PM)</li>
            <li>2 Group Classes per Week</li>
            <li>No Personal Trainer</li>
        </ul>
        <a href="basic_register.php" class="btn">Join Now</a>
    </div>

    <div class="plan popular">
        <div class="plan-icon">
            <img src="assets/img/premium-plan-icon.png" alt="Premium Plan Icon" />
        </div>
        <h2>Premium Plan</h2>
        <p class="price">Rs. 5,000 / month</p>
        <ul>
            <li>Full Day Gym Access</li>
            <li>Unlimited Group Classes</li>
            <li>1 Personal Training Session / Week</li>
        </ul>
        <a href="premium_register.php" class="btn">Join Now</a>
    </div>

    <div class="plan">
        <div class="plan-icon">
            <img src="assets/img/elite-plan-icon.png" alt="Elite Plan Icon" />
        </div>
        <h2>Elite Plan</h2>
        <p class="price">Rs. 8,000 / month</p>
        <ul>
            <li>Full Access + Weekend Use</li>
            <li>Unlimited Group Classes</li>
            <li>Personal Trainer + Nutrition Guide</li>
        </ul>
        <a href="elite_register.php" class="btn">Join Now</a>
    </div>
</section>


    <!-- Membership Benefits Section -->
    <section class="membership-benefits">
        <h2>All Plans Include:</h2>
        <ul class="benefits-list">
            <li>Access to modern equipment</li>
            <li>Changing rooms & lockers</li>
            <li>Free WiFi & Parking</li>
            <li>Member mobile app access</li>
        </ul>
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
