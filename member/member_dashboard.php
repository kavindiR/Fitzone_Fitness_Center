<?php
// member_dashboard.php
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
// Check for success message
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}


// Sample user data - in a real app this would come from your database
$userData = [
    'fullname' => $_SESSION['fullname'] ?? 'Member',
    'membership_plan' => $_SESSION['membership_plan'] ?? 'Basic Plan',
    'membership_since' => $_SESSION['membership_since'] ?? date('F Y', strtotime('-3 months')),
    'classes' => $_SESSION['classes'] ?? ['Yoga', 'Cardio'],
    'next_class' => $_SESSION['next_class'] ?? 'Yoga - Tomorrow at 6:00 PM',
    'workout_plan' => $_SESSION['workout_plan'] ?? [
        'Monday' => [
            ['name' => 'Bench Press', 'sets' => 4, 'reps' => 8],
            ['name' => 'Pull-ups', 'sets' => 3, 'reps' => 10]
        ],
        'Wednesday' => [
            ['name' => 'Squats', 'sets' => 4, 'reps' => 8],
            ['name' => 'Deadlifts', 'sets' => 3, 'reps' => 6]
        ]
    ],
    'stats' => [
        'workouts_this_week' => $_SESSION['stats']['workouts_this_week'] ?? 4,
        'active_streak' => $_SESSION['stats']['active_streak'] ?? 12,
        'calories_burned' => $_SESSION['stats']['calories_burned'] ?? 3450,
        'weight_progress' => $_SESSION['stats']['weight_progress'] ?? -2.5
    ]
];

// Determine membership benefits based on plan
$membershipBenefits = [
    'Basic Plan' => ['Gym Access (8AM-6PM)', '2 Group Classes/Week', 'No Personal Trainer'],
    'Premium Plan' => ['Full Day Access', 'Unlimited Classes', '1 PT Session/Week'],
    'Elite Plan' => ['24/7 Access', 'Unlimited Classes', 'Personal Trainer', 'Nutrition Guide']
];

// Determine icon based on membership plan
$planIcon = [
    'Basic Plan' => 'fa-id-card',
    'Premium Plan' => 'fa-crown',
    'Elite Plan' => 'fa-trophy'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
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

        /* Header - Matching index.php */
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
            gap: 10px;
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-left: 5px solid var(--primary);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(164, 0, 0, 0.3);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 1.1rem;
            color: var(--text-secondary);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(164, 0, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            margin: 10px 0;
            font-family: 'Anton', sans-serif;
        }

        .stat-change {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .stat-change.positive {
            color: #4cc9f0;
        }

        /* Membership Info Card */
        .membership-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-top: 5px solid var(--primary);
        }

        .membership-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .membership-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 15px;
        }

        .membership-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.5rem;
            color: white;
            letter-spacing: 1px;
        }

        .membership-since {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .benefits-list {
            margin-top: 20px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .benefit-item:last-child {
            border-bottom: none;
        }

        .benefit-item i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Classes Section */
        .classes-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .classes-section {
                grid-template-columns: 1fr;
            }
        }

        .classes-card, .next-class-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .classes-card {
            border-bottom: 5px solid #4cc9f0;
        }

        .next-class-card {
            border-bottom: 5px solid #f8961e;
        }

        .classes-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.3rem;
            color: white;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .class-tag {
            display: inline-block;
            background-color: rgba(164, 0, 0, 0.2);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 20px;
            margin: 5px;
            font-size: 0.9rem;
        }

        .next-class-info {
            font-size: 1.1rem;
            color: var(--text);
            margin-top: 15px;
        }

        /* Workout Plan Section */
        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 2rem;
            color: white;
            margin: 30px 0 20px;
            letter-spacing: 1px;
        }

        .workout-plan {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .workout-day {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .workout-day:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .day-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.3rem;
            color: var(--primary);
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .exercise {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            margin-bottom: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .exercise:hover {
            background-color: rgba(164, 0, 0, 0.2);
            transform: translateX(5px);
        }

        .exercise-name {
            font-weight: 700;
            color: white;
        }

        .exercise-details {
            color: var(--text-secondary);
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

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header Matching index.php -->
    <header class="dashboard-header">
        <a href="member_dashboard.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="user-nav">
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($userData['fullname']) ?>&background=random" alt="User">
                <span class="user-name"><?= htmlspecialchars($userData['fullname']) ?></span>
            </div>
            <a href="../logout.php" class="btn-logout">Logout</a>
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
                <h1 class="welcome-title">Welcome Back, <?= htmlspecialchars($userData['fullname']) ?>!</h1>
                <p class="welcome-subtitle">Track your fitness journey and achieve your goals with FitZone</p>
            </section>

            <!-- Membership Info -->
            <div class="membership-card">
                <div class="membership-header">
                    <i class="fas <?= $planIcon[$userData['membership_plan']] ?> membership-icon"></i>
                    <div>
                        <h2 class="membership-title"><?= $userData['membership_plan'] ?></h2>
                        <p class="membership-since">Member since <?= $userData['membership_since'] ?></p>
                    </div>
                </div>
                <div class="benefits-list">
                    <h3 class="classes-title">Your Membership Benefits:</h3>
                    <?php foreach ($membershipBenefits[$userData['membership_plan']] as $benefit): ?>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?= $benefit ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Workouts This Week</h3>
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $userData['stats']['workouts_this_week'] ?></div>
                    <div class="stat-change positive">+2 from last week</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Active Streak</h3>
                        <div class="stat-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $userData['stats']['active_streak'] ?></div>
                    <div class="stat-change">days</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Calories Burned</h3>
                        <div class="stat-icon">
                            <i class="fas fa-burn"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($userData['stats']['calories_burned']) ?></div>
                    <div class="stat-change positive">+15% from last week</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Weight Progress</h3>
                        <div class="stat-icon">
                            <i class="fas fa-weight"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $userData['stats']['weight_progress'] ?>kg</div>
                    <div class="stat-change positive">since last month</div>
                </div>
            </div>

            <!-- Classes Section -->
            <div class="classes-section">
                <div class="classes-card">
                    <h3 class="classes-title">Your Classes</h3>
                    <?php foreach ($userData['classes'] as $class): ?>
                        <span class="class-tag"><?= $class ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="next-class-card">
                    <h3 class="classes-title">Next Scheduled Class</h3>
                    <p class="next-class-info">
                        <i class="fas fa-calendar-day"></i> <?= $userData['next_class'] ?>
                    </p>
                </div>
            </div>                   
</body>
</html>