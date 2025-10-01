<?php
// login.php
session_start();
require_once 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validation
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                // Set success message in session
                $_SESSION['login_success'] = "Successfully logged in! Welcome back, " . htmlspecialchars($user['fullname']) . "!";
                
                // Regenerate session ID
                session_regenerate_id(true);
                
                // Redirect based on role
                $redirect_page = 'member/member_dashboard.php';
                if ($user['role'] == 'admin') {
                    $redirect_page = 'admin/admin_dashboard.php';
                } elseif ($user['role'] == 'trainer') {
                    $redirect_page = 'staff/staff_dashboard.php';
                }
                
                header("Location: $redirect_page");
                exit();
            } else {
                $errors['login'] = 'Invalid email or password';
            }
        } else {
            $errors['login'] = 'Invalid email or password';
        }

        $stmt->close();
    }
}

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $registration_success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - FitZone</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* ================================================ Login =============================================== */
        /* Form Container */
        .form-container {
            padding: 80px 40px;
            text-align: center;
            background-color: var(--bg-light);
            max-width: 500px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.5s ease-out;
        }

        .form-container h2 {
            font-size: 2.5rem;
            color: var(--main-color);
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-out;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .login-form label {
            font-size: 1.1rem;
            color: var(--text-color);
            font-weight: bold;
            display: block;
            text-align: left;
        }

        .login-form input {
            padding: 12px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #444;
            background-color: #333;
            color: var(--text-color);
            transition: border-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .login-form input:focus {
            border-color: var(--main-color);
            outline: none;
        }

        .login-form button {
            background-color: var(--main-color);
            color: #fff;
            padding: 12px 20px;
            font-size: 1.2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            width: 100%;
        }

        .login-form button:hover {
            background-color: var(--secondary-color);
            transform: scale(1.02);
        }

        .login-form p {
            font-size: 1rem;
            color: #ddd;
            margin-top: 20px;
        }

        .login-form a {
            color: var(--accent-color);
            text-decoration: underline;
        }

        .login-form a:hover {
            color: var(--main-color);
        }

        .error {
            color: #ff3333;
            font-size: 0.9rem;
            margin-top: 5px;
            text-align: left;
        }

        .error-message {
            color: #ff3333;
            background-color: #ffebee;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }

        /* Success message styles */
        .success-message {
            color: #4BB543;
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c8e6c9;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 30px;
            background-color: #000;
            color: #aaa;
            border-top: 2px solid var(--main-color);
        }

        footer p {
            font-size: 1rem;
            color: #ccc;
        }

        /* Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .form-container {
                padding: 40px 20px;
            }
            .form-container h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .form-container h2 {
                font-size: 1.8rem;
            }
            .login-form button {
                padding: 10px 15px;
                font-size: 1.1rem;
            }
            .login-form label {
                font-size: 1rem;
            }
            .login-form input {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .form-container h2 {
                font-size: 1.6rem;
            }
            .login-form button {
                padding: 10px 12px;
                font-size: 1rem;
            }
            footer p {
                font-size: 0.8rem;
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
</head>
<body>
    <header class="main-header">
        <a href="index.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="membership.php">Membership</a>
            <a href="classes.php">Classes</a>
            <a href="contact.php">Contact</a>
            <a href="register.php" class="btn-register">Register</a>
        </nav>
    </header>

    <section class="form-container">
        <h2>Login to Your Account</h2>

        <?php if (isset($registration_success)): ?>
            <div class="success-message"><?php echo $registration_success; ?></div>
        <?php endif; ?>

        <?php if (!empty($errors['login'])): ?>
            <div class="error-message"><?php echo $errors['login']; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="login-form" novalidate>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required />
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />
                <?php if (!empty($errors['password'])): ?>
                    <div class="error"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
        </form>
    </section>

    <footer class="site-footer">
        <div class="footer-container">
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