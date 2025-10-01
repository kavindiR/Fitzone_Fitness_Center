<?php
// register_class.php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['user_id'];
    $class_name = $_POST['class_name'];
    
    try {
        // Check if already registered for this class
        $check_stmt = $conn->prepare("SELECT * FROM class_registrations WHERE member_id = ? AND class_name = ?");
        $check_stmt->bind_param('is', $member_id, $class_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "You are already registered for this class!";
        } else {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO class_registrations (member_id, class_name) VALUES (?, ?)");
            $stmt->bind_param('is', $member_id, $class_name);
            $stmt->execute();
            
            // Update session with new class info
            if (!isset($_SESSION['classes'])) {
                $_SESSION['classes'] = [];
            }
            $_SESSION['classes'][] = $class_name;
            
            // Redirect to dashboard with success message
            $_SESSION['success'] = "Successfully registered for $class_name class!";
            header("Location: member_dashboard.php");
            exit();
        }
    } catch(mysqli_sql_exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch available classes (same as in index.php)
$availableClasses = [
    [
        'name' => 'Strength Training',
        'description' => 'Build muscle and improve endurance.',
        'icon' => 'fa-dumbbell'
    ],
    [
        'name' => 'Cardio Sessions',
        'description' => 'Boost your stamina and burn calories fast.',
        'icon' => 'fa-heartbeat'
    ],
    [
        'name' => 'Yoga & Flexibility',
        'description' => 'Improve posture and relax your body and mind.',
        'icon' => 'fa-spa'
    ],
    [
        'name' => 'Pilates',
        'description' => 'Core strength and overall body flexibility.',
        'icon' => 'fa-ring'
    ],
    [
        'name' => 'Zumba',
        'description' => 'Dance fitness for weight loss and fun.',
        'icon' => 'fa-music'
    ],
    [
        'name' => 'CrossFit',
        'description' => 'High-intensity workout for strength and stamina.',
        'icon' => 'fa-fire-alt'
    ],
    [
        'name' => 'Weight Loss',
        'description' => 'Targeted training for effective fat loss and toning.',
        'icon' => 'fa-weight'
    ],
    [
        'name' => 'Boxing',
        'description' => 'Cardio and strength training through boxing.',
        'icon' => 'fa-boxing-glove'
    ]
];

// Fetch user's current classes from database
$current_classes = [];
if (isset($_SESSION['user_id'])) {
    $member_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT class_name FROM class_registrations WHERE member_id = ?");
    $stmt->bind_param('i', $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $current_classes[] = $row['class_name'];
    }
    
    // Update session
    $_SESSION['classes'] = $current_classes;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register to a Class - FitZone</title>
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

        /* Classes Grid */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .class-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            border-left: 5px solid var(--primary);
        }

        .class-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(164, 0, 0, 0.3);
        }

        .class-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .class-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 15px;
            width: 60px;
            height: 60px;
            background-color: rgba(164, 0, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .class-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.8rem;
            color: white;
            letter-spacing: 1px;
        }

        .class-description {
            color: var(--text-secondary);
            margin: 20px 0;
            line-height: 1.6;
        }

        .btn-register {
            background-color: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-register:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-registered {
            background-color: #28a745;
            cursor: not-allowed;
        }

        /* Current Classes Section */
        .current-classes {
            background-color: rgba(76, 201, 240, 0.1);
            border: 1px solid #4cc9f0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .current-classes-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.5rem;
            color: #4cc9f0;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .class-tag {
            display: inline-block;
            background-color: rgba(76, 201, 240, 0.2);
            color: #4cc9f0;
            padding: 5px 15px;
            border-radius: 20px;
            margin: 5px;
            font-size: 0.9rem;
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

            .classes-grid {
                grid-template-columns: 1fr;
            }
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
                <h1 class="welcome-title">Register to a Class</h1>
                <p class="welcome-subtitle">Choose from our variety of fitness classes to enhance your workout routine</p>
            </section>

            <?php if (!empty($current_classes)): ?>
                <div class="current-classes">
                    <h3 class="current-classes-title">Your Current Classes</h3>
                    <?php foreach ($current_classes as $class): ?>
                        <span class="class-tag"><?= htmlspecialchars($class) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="classes-grid">
                <?php foreach ($availableClasses as $class): ?>
                    <div class="class-card">
                        <div class="class-header">
                            <div class="class-icon">
                                <i class="fas <?= $class['icon'] ?>"></i>
                            </div>
                            <h2 class="class-title"><?= $class['name'] ?></h2>
                        </div>
                        <p class="class-description"><?= $class['description'] ?></p>
                        <form method="POST">
                            <input type="hidden" name="class_name" value="<?= $class['name'] ?>">
                            <button type="submit" class="btn-register <?= in_array($class['name'], $current_classes) ? 'btn-registered' : '' ?>" 
                                <?= in_array($class['name'], $current_classes) ? 'disabled' : '' ?>>
                                <?= in_array($class['name'], $current_classes) ? 'Already Registered' : 'Register Now' ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>