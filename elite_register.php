<?php
include 'db_connect.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($fullname)) $errors['fullname'] = "Full name is required";
    if (empty($email)) $errors['email'] = "Email is required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format";
    if (empty($phone)) $errors['phone'] = "Phone number is required";
    elseif (strlen($phone) < 10) $errors['phone'] = "Phone number must be at least 10 digits";
    if (empty($password)) $errors['password'] = "Password is required";
    elseif (strlen($password) < 8) $errors['password'] = "Password must be at least 8 characters";
    if ($password != $confirm_password) $errors['confirm_password'] = "Passwords do not match";

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $membership_id = 'FZ-ELITE-' . date('Y') . '-' . strtoupper(substr(uniqid(), 7));
        $join_date = date('Y-m-d');
        $renewal_date = date('Y-m-d', strtotime('+1 month'));
        
        $stmt = $conn->prepare("INSERT INTO members (fullname, email, phone, password, membership_id, membership_plan, join_date, renewal_date) VALUES (?, ?, ?, ?, ?, 'elite', ?, ?)");
        $stmt->bind_param("sssssss", $fullname, $email, $phone, $hashed_password, $membership_id, $join_date, $renewal_date);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors['database'] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Elite Plan Registration - FitZone</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header Section -->
    <header class="main-header">
        <a href="index.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="membership.php">Membership</a>
            <a href="classes.php">Classes</a>
            <a href="blog.php">Blogs</a>
            <a href="contact.php">Contact</a>
            <a href="shop.php">Shop</a>
        </nav>
    </header>

    <div class="registration-container">
        <h1>Elite Plan Registration</h1>
        
        <div class="plan-highlight">
            <h2>Elite Plan</h2>
            <p class="price">Rs. 8,000 / month</p>
            <ul>
                <li>24/7 Gym Access (Including Weekends)</li>
                <li>Unlimited Group Classes</li>
                <li>Personal Trainer (3 Sessions/Week)</li>
                <li>Custom Nutrition Guide</li>
                <li>VIP Locker + Spa Access</li>
                <li>Priority Booking for Classes</li>
            </ul>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                <h2>Registration Successful!</h2>
                <p>Your membership ID: <strong><?php echo $membership_id; ?></strong></p>
                <p>Welcome to FitZone Elite! Your membership will be activated within 24 hours.</p>
                <p>A nutrition specialist will contact you within 48 hours to discuss your personalized plan.</p>
            </div>
        <?php else: ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>" required>
                    <?php if (!empty($errors['fullname'])): ?>
                        <span class="error"><?php echo $errors['fullname']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                    <?php if (!empty($errors['phone'])): ?>
                        <span class="error"><?php echo $errors['phone']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($errors['database'])): ?>
                    <div class="error" style="text-align: center; margin: 20px 0;">
                        <?php echo $errors['database']; ?>
                    </div>
                <?php endif; ?>
                
                <div class="submit-btn">
                    <button type="submit">Become an Elite Member</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }
        
        .registration-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #1f1f1f;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .registration-container h1 {
            text-align: center;
            color: #c1272d;
            font-family: 'Anton', sans-serif;
            margin-bottom: 30px;
            font-size: 2.5rem;
        }
        
        .plan-highlight {
            background: #292929;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            border: 2px solid #c1272d;
        }
        
        .plan-highlight h2 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .plan-highlight .price {
            color: #c1272d;
            font-size: 1.8rem;
            font-weight: bold;
            margin: 15px 0;
        }
        
        .plan-highlight ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .plan-highlight ul li {
            margin: 10px 0;
            position: relative;
            padding-left: 25px;
        }
        
        .plan-highlight ul li:before {
            content: "✓";
            color: #c1272d;
            position: absolute;
            left: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 8px;
            background: #333;
            color: #fff;
            font-size: 1rem;
        }
        
        .error {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .submit-btn {
            text-align: center;
            margin-top: 30px;
        }
        
        button[type="submit"] {
            background: #c1272d;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        
        button[type="submit"]:hover {
            background: #a51f25;
            transform: scale(1.05);
        }
        
        .success-message {
            text-align: center;
            padding: 20px;
            background: #2e7d32;
            color: white;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 FitZone Fitness Center. All rights reserved.</p>
    </footer>

</body>
</html>