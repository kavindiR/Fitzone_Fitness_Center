<?php
// profile.php
session_start();

// Check if user is logged in and is a member
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit();
}

// Database connection
require_once('../db_connect.php');

// Initialize variables
$error = '';
$success = '';
$profile = [
    'fullname' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'gender' => '',
    'birthdate' => '',
    'height' => '',
    'weight' => '',
    'fitness_goals' => '',
    'medical_conditions' => ''
];

// Fetch existing profile data
if (isset($_SESSION['user_id'])) {
    $member_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM member_profiles WHERE member_id = ?");
    $stmt->bind_param('i', $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    }
    
    // Always get the latest name and email from session
    $profile['fullname'] = $_SESSION['fullname'] ?? '';
    $profile['email'] = $_SESSION['email'] ?? '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['user_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $height = $_POST['height'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $fitness_goals = $_POST['fitness_goals'] ?? '';
    $medical_conditions = $_POST['medical_conditions'] ?? '';
    
    try {
        // Check if profile exists
        $check_stmt = $conn->prepare("SELECT id FROM member_profiles WHERE member_id = ?");
        $check_stmt->bind_param('i', $member_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing profile
            $stmt = $conn->prepare("
                UPDATE member_profiles SET 
                fullname = ?, email = ?, phone = ?, address = ?, gender = ?, 
                birthdate = ?, height = ?, weight = ?, fitness_goals = ?, medical_conditions = ?
                WHERE member_id = ?
            ");
            $stmt->bind_param('ssssssddssi', 
                $fullname, $email, $phone, $address, $gender, 
                $birthdate, $height, $weight, $fitness_goals, $medical_conditions, 
                $member_id
            );
        } else {
            // Insert new profile
            $stmt = $conn->prepare("
                INSERT INTO member_profiles 
                (member_id, fullname, email, phone, address, gender, birthdate, height, weight, fitness_goals, medical_conditions)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('issssssdsss', 
                $member_id, $fullname, $email, $phone, $address, $gender, 
                $birthdate, $height, $weight, $fitness_goals, $medical_conditions
            );
        }
        
        if ($stmt->execute()) {
            // Update session with new name and email
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;
            
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } catch(mysqli_sql_exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Use the same styles as member_dashboard.php for consistency */
        :root {
            --primary: #a40000;
            --primary-hover: #c1272d;
            --dark: #000000;
            --light: #f8f9fa;
            --text: #ffffff;
            --text-secondary: #ddd;
            --card-bg: #1a1a1a;
            --border: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: var(--dark);
            color: var(--text);
            min-height: 100vh;
        }

        /* Header - Matching member_dashboard.php */
        .dashboard-header {
            background-color: var(--dark);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid var(--primary);
        }

        .dashboard-header .logo {
            font-family: 'Anton', sans-serif;
            font-size: 2rem;
            color: white;
            text-decoration: none;
        }

        .dashboard-header .logo span {
            color: var(--primary);
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }

        .user-name {
            font-weight: 700;
        }

        .btn-logout {
            background-color: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
            padding-top: 70px; /* Account for fixed header */
        }

        /* Sidebar */
        .sidebar {
            background-color: #111;
            padding: 30px 0;
            height: calc(100vh - 70px);
            position: fixed;
            width: 250px;
            border-right: 1px solid var(--border);
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 15px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .menu-item i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 15px;
            color: var(--primary);
        }

        .menu-item:hover, .menu-item.active {
            background-color: var(--primary);
            color: white;
            transform: translateX(5px);
        }

        .menu-item:hover i, .menu-item.active i {
            color: white;
        }

        /* Main Content */
        .main-content {
            grid-column: 2;
            padding: 30px;
            margin-left: 250px;
        }

        .welcome-section {
            margin-bottom: 40px;
        }

        .welcome-title {
            font-family: 'Anton', sans-serif;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .welcome-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Profile Form */
        .profile-form {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-col {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn-submit {
            background-color: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Error/Success Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 700;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .main-content {
                grid-column: 1;
                margin-left: 0;
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                padding: 15px;
                text-align: center;
            }

            .user-nav {
                margin-top: 15px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header Matching member_dashboard.php -->
    <header class="dashboard-header">
        <a href="member_dashboard.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="user-nav">
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['fullname'] ?? 'Member') ?>&background=random" alt="User">
                <span class="user-name"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Member') ?></span>
            </div>
            <a href="/fitzone/logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-menu">
                <a href="member_dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="register_membership.php" class="menu-item">
                    <i class="fas fa-id-card"></i>
                    Membership Registration
                </a>
                <a href="register_class.php" class="menu-item">
                    <i class="fas fa-calendar-plus"></i>
                    Class Registration
                </a>
                <a href="book_training.php" class="menu-item">
                    <i class="fas fa-dumbbell"></i>
                    Training Session Booking 
                </a>
                <a href="my_schedules.php" class="menu-item">
                    <i class="fas fa-user"></i>
                    My Schedules
                </a>
                <a href="profile.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    Profile
                </a> 
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <section class="welcome-section">
                <h1 class="welcome-title">My Profile</h1>
                <p class="welcome-subtitle">Update your personal information and fitness preferences</p>
            </section>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" 
                                   value="<?= htmlspecialchars($profile['fullname']) ?>" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="<?= htmlspecialchars($profile['email']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control" 
                                   value="<?= htmlspecialchars($profile['phone']) ?>">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" <?= $profile['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $profile['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $profile['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" id="address" class="form-control"><?= htmlspecialchars($profile['address']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" id="birthdate" class="form-control" 
                                   value="<?= htmlspecialchars($profile['birthdate']) ?>">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="height" class="form-label">Height (cm)</label>
                            <input type="number" name="height" id="height" class="form-control" 
                                   value="<?= htmlspecialchars($profile['height']) ?>" step="0.1" min="0">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" id="weight" class="form-control" 
                                   value="<?= htmlspecialchars($profile['weight']) ?>" step="0.1" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fitness_goals" class="form-label">Fitness Goals</label>
                    <textarea name="fitness_goals" id="fitness_goals" class="form-control"><?= htmlspecialchars($profile['fitness_goals']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="medical_conditions" class="form-label">Medical Conditions (if any)</label>
                    <textarea name="medical_conditions" id="medical_conditions" class="form-control"><?= htmlspecialchars($profile['medical_conditions']) ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Save Profile</button>
            </form>
        </main>
    </div>
</body>
</html>