<?php
// Start session and include database connection
session_start();
require_once __DIR__ . '/db_connect.php';

$pageTitle = "Contact - FitZone";

// Initialize variables
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? $conn->real_escape_string(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? $conn->real_escape_string(trim($_POST['message'])) : '';
    
    // Handle file upload
    $file_path = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/assets/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = basename($_FILES['attachment']['name']);
        $file_path = '/assets/uploads/' . uniqid() . '_' . $file_name;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], __DIR__ . $file_path)) {
            $file_path = $conn->real_escape_string($file_path);
        }
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $query = "INSERT INTO inquiries (user_id, name, email, subject, message, file_path) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $name, $email, $subject, $message, $file_path);
    
    if ($stmt->execute()) {
        $success_message = "Thank you for your message! We'll get back to you soon.";
    } else {
        $error_message = "There was an error submitting your form. Please try again.";
    }
    
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?></title>
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
    </header>

    <style>
        /* Contact Intro Section */
        .contact-intro {
            padding: 80px 40px;
            text-align: center;
            background-color: #000000;
        }

        .contact-intro h1 {
            font-size: 3.5rem;
            color: var(--main-color);
            margin-bottom: 20px;
            animation: fadeInUp 1.5s ease-out;
        }

        .contact-intro p {
            font-size: 1.2rem;
            color: #ddd;
            animation: fadeInUp 1.8s ease-out;
        }

        /* Contact Form Section */
        .contact-form-section {
            padding: 60px 40px;
            background-color: #222;
            border-radius: 10px;
            margin: 40px auto;
            max-width: 800px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-form label {
            font-size: 1.1rem;
            color: var(--text-color);
            font-weight: bold;
        }

        .contact-form input,
        .contact-form textarea {
            padding: 12px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #444;
            background-color: #333;
            color: var(--text-color);
            transition: border-color 0.3s ease;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: var(--main-color);
            outline: none;
        }

        .contact-form button {
            background-color: var(--main-color);
            color: #fff;
            padding: 12px 20px;
            font-size: 1.2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .contact-form button:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        /* Contact Info Section */
        .contact-info {
            padding: 60px 40px;
            background-color: var(--bg-light);
            color: #ddd;
            text-align: center;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-info h2 {
            font-size: 2rem;
            color: var(--main-color);
            margin-bottom: 20px;
            animation: fadeInUp 1.8s ease-out;
        }

        .contact-info p {
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 20px;
            animation: fadeInUp 2s ease-out;
        }

        /* Alerts */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #4CAF50;
            color: white;
        }

        .alert-error {
            background-color: #f44336;
            color: white;
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

    <section class="contact-intro">
        <h1>Get in Touch</h1>
        <p>Have a question? Need help with a membership? We'd love to hear from you.</p>
    </section>

    <section class="contact-form-section">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form action="contact.php" method="POST" class="contact-form" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
            
            <label for="message">Your Message:</label>
            <textarea name="message" id="message" rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            
            <label for="attachment">Attachment (Optional):</label>
            <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            
            <button type="submit">Send Message</button>
        </form>
    </section>

    <section class="contact-info">
        <h2>Contact Details</h2>
        <p><strong>Address:</strong> No. 21, Main Street, Kurunegala, Sri Lanka</p>
        <p><strong>Phone:</strong> +94 77 123 4567</p>
        <p><strong>Email:</strong> support@fitzone.lk</p>
        <p><strong>Working Hours:</strong> Mon - Sat: 6AM - 10PM</p>
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