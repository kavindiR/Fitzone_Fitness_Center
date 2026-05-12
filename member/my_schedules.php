<?php
// my_schedules.php
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

// Fetch member's registered classes
$member_id = $_SESSION['user_id'];
$registered_classes = [];
$class_stmt = $conn->prepare("SELECT class_name FROM class_registrations WHERE member_id = ?");
$class_stmt->bind_param('i', $member_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();

while ($row = $class_result->fetch_assoc()) {
    $registered_classes[] = $row['class_name'];
}

// Define the weekly schedule (same as classes.php)
$weekly_schedule = [
    'Monday' => [
        '6:00 AM' => 'Yoga',
        '10:00 AM' => 'Strength Training',
        '6:00 PM' => 'CrossFit',
        '8:00 PM' => 'Body Pump'
    ],
    'Tuesday' => [
        '6:00 AM' => 'HIIT',
        '10:00 AM' => 'Core Blast',
        '6:00 PM' => 'Zumba',
        '8:00 PM' => 'Yoga'
    ],
    'Wednesday' => [
        '6:00 AM' => 'Cardio Burn',
        '10:00 AM' => 'Body Pump',
        '6:00 PM' => 'HIIT',
        '8:00 PM' => 'Strength Training'
    ],
    'Thursday' => [
        '6:00 AM' => 'Yoga',
        '10:00 AM' => 'Strength Training',
        '6:00 PM' => 'Cardio Burn',
        '8:00 PM' => 'Zumba'
    ],
    'Friday' => [
        '6:00 AM' => 'HIIT',
        '10:00 AM' => 'Boxing',
        '6:00 PM' => 'CrossFit',
        '8:00 PM' => 'Body Sculpt'
    ],
    'Saturday' => [
        '6:00 AM' => 'Zumba',
        '10:00 AM' => '--',
        '6:00 PM' => 'Yoga',
        '8:00 PM' => '--'
    ]
];

// Filter schedule to only show classes the member is registered for
$member_schedule = [];
foreach ($weekly_schedule as $day => $times) {
    foreach ($times as $time => $class) {
        if (in_array($class, $registered_classes)) {
            if (!isset($member_schedule[$day])) {
                $member_schedule[$day] = [];
            }
            $member_schedule[$day][$time] = $class;
        }
    }
}

// Count total classes per week
$total_classes = 0;
foreach ($member_schedule as $day => $classes) {
    $total_classes += count($classes);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedules - FitZone</title>
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

        /* Header */
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
            padding-top: 70px;
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

        /* Summary Card */
        .summary-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-left: 5px solid var(--primary);
        }

        .summary-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary-item {
            flex: 1;
            min-width: 200px;
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Anton', sans-serif;
        }

        .summary-label {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Schedule Table */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .schedule-table th {
            background-color: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
            font-family: 'Anton', sans-serif;
            letter-spacing: 1px;
        }

        .schedule-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        .schedule-table tr:last-child td {
            border-bottom: none;
        }

        .schedule-table tr:hover td {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .time-cell {
            font-weight: 700;
            color: var(--primary);
        }

        .no-classes {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .no-classes-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .no-classes-message {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .btn-register {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
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

            .schedule-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
                <h1 class="welcome-title">My Class Schedule</h1>
                <p class="welcome-subtitle">View your weekly class schedule and attendance</p>
            </section>

            <div class="summary-card">
                <div class="summary-content">
                    <div class="summary-item">
                        <div class="summary-value"><?= count($registered_classes) ?></div>
                        <div class="summary-label">Registered Classes</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value"><?= $total_classes ?></div>
                        <div class="summary-label">Weekly Sessions</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">
                            <?php 
                            $days_with_classes = count($member_schedule);
                            echo $days_with_classes > 0 ? $days_with_classes : '0';
                            ?>
                        </div>
                        <div class="summary-label">Active Days</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($member_schedule)): ?>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Class</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($member_schedule as $day => $classes): ?>
                            <?php foreach ($classes as $time => $class): ?>
                                <tr>
                                    <td><?= htmlspecialchars($day) ?></td>
                                    <td class="time-cell"><?= htmlspecialchars($time) ?></td>
                                    <td><?= htmlspecialchars($class) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-classes">
                    <div class="no-classes-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <h3 class="no-classes-message">Join our exciting classes to enhance your fitness journey!</h3>
                    <a href="register_class.php" class="btn-register">Register for Classes</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>