<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - FitZone</title>
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
        /* Blog Intro */
        .blog-intro {
            background: url('assets/img/blog-banner.png') center/cover no-repeat;
            height: 100vh;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            color: white;
            padding: 0 20px;
        }

        .blog-intro::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .blog-intro-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .blog-intro h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
            font-family: 'Anton', sans-serif;
            letter-spacing: 2px;
        }

        .blog-intro p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }

        /* Blog Posts */
        .blog-posts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 60px 10%;
            background: #1a1a1a;
        }

        .post {
            background: #222;
            padding: 30px;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: zoomIn 1s ease forwards;
        }

            .post:hover {
                transform: translateY(-10px);
                box-shadow: 0 10px 20px rgba(161, 0, 0, 0.4);
            }

            .post h2 {
                font-size: 24px;
                color: #ff4b4b;
                margin-bottom: 15px;
            }

        .meta {
            font-size: 14px;
            color: #999;
            margin-bottom: 20px;
        }

        .post p {
            font-size: 16px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .post a {
            text-decoration: none;
            color: #a10000;
            font-weight: bold;
            border: 2px solid #a10000;
            padding: 8px 15px;
            border-radius: 30px;
            transition: background 0.3s, color 0.3s;
        }

            .post a:hover {
                background: #a10000;
                color: #fff;
            }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px 0;
            background: #000;
            font-size: 14px;
            color: #aaa;
            margin-top: 50px;
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes zoomIn {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                padding: 20px;
            }

            .nav-links {
                margin-top: 15px;
                text-align: center;
            }

                .nav-links a {
                    margin: 10px;
                    display: inline-block;
                }

            .blog-intro h1 {
                font-size: 2.5rem;
            }
            
            .blog-intro p {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 480px) {
            .blog-intro h1 {
                font-size: 2rem;
            }
            
            .blog-intro p {
                font-size: 1rem;
            }
        }
        
        /* Slider inside blog cards */
        .slider {
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            margin-bottom: 20px;
        }

            .slider img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                position: absolute;
                opacity: 0;
                transition: opacity 1s ease-in-out;
            }

                .slider img.active {
                    opacity: 1;
                }

        /* Blog card animation on scroll */
        .post {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.7s ease-in-out;
        }

            .post.show {
                opacity: 1;
                transform: translateY(0px);
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

    <section class="blog-intro">
        <div class="blog-intro-content">
            <h1>FitZone Blog</h1>
            <p>Stay informed with expert fitness advice, tips, and the latest news from our team.</p>
        </div>
    </section>

    <section class="blog-posts">
        <!-- Blog Card 1 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog1-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog1-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog1-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>5 Tips to Stay Motivated at the Gym</h2>
            <p class="meta">Published on March 25, 2025 | By Coach Amila</p>
            <p>Staying consistent with workouts can be tough, but these 5 motivation hacks will keep you going strong...</p>
            <a href="blog1.html">Read More</a>
        </article>

        <!-- Blog Card 2 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog2-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog2-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog2-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>The Best Post-Workout Meals for Muscle Recovery</h2>
            <p class="meta">Published on March 12, 2025 | By Sachini Fernando</p>
            <p>Fuel your gains the right way! Here are top meals that help your muscles recover and grow after a hard session...</p>
            <a href="blog2.html">Read More</a>
        </article>

        <!-- Blog Card 3 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog3-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog3-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog3-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>Why Group Classes Can Boost Your Fitness Journey</h2>
            <p class="meta">Published on February 28, 2025 | By Nuwan Jayasuriya</p>
            <p>Working out in a group is not just more fun — it also increases accountability and consistency...</p>
            <a href="blog3.html">Read More</a>
        </article>

        <!-- Blog Card 4 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog4-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog4-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog4-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>Stretching: The Key to Better Performance</h2>
            <p class="meta">Published on February 15, 2025 | By Ishara Perera</p>
            <p>Never skip your stretches! Learn why flexibility work is crucial for peak performance and injury prevention...</p>
            <a href="blog4.html">Read More</a>
        </article>

        <!-- Blog Card 5 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog5-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog5-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog5-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>How Sleep Affects Your Workout Results</h2>
            <p class="meta">Published on February 5, 2025 | By Ruwan Fernando</p>
            <p>Maximize your progress by understanding the powerful link between sleep and strength gains...</p>
            <a href="blog5.html">Read More</a>
        </article>

        <!-- Blog Card 6 -->
        <article class="post">
            <div class="slider">
                <img src="assets/img/blog6-1.png" alt="Blog Image 1" class="slide active">
                <img src="assets/img/blog6-2.png" alt="Blog Image 2" class="slide">
                <img src="assets/img/blog6-3.png" alt="Blog Image 3" class="slide">
            </div>
            <h2>Best Cardio Workouts for Fat Loss</h2>
            <p class="meta">Published on January 20, 2025 | By Thilina Weerasinghe</p>
            <p>Burn more fat efficiently by choosing the right type of cardio for your body and fitness goals...</p>
            <a href="blog6.html">Read More</a>
        </article>
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
   

    <script src="assets/js/slider.js"></script>

    <script>
        // Simple script to animate blog posts when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const posts = document.querySelectorAll('.post');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('show');
                    }
                });
            }, { threshold: 0.1 });
            
            posts.forEach(post => {
                observer.observe(post);
            });
            
            // Simple image slider for blog cards
            const sliders = document.querySelectorAll('.slider');
            sliders.forEach(slider => {
                const slides = slider.querySelectorAll('.slide');
                let currentSlide = 0;
                
                function nextSlide() {
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }
                
                setInterval(nextSlide, 3000);
            });
        });
    </script>
    
   
</body>
</html>