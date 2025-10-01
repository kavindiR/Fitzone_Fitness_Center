<?php
// staff/staff_dashboard.php
session_start();

// Check if user is logged in and is a trainer/staff
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'trainer') {
    header("Location: ../login.php");
    exit();
}

// Sample trainer data - in a real app this would come from your database
$trainerData = [
    'fullname' => $_SESSION['fullname'] ?? 'Trainer',
    'specialization' => $_SESSION['specialization'] ?? 'Strength & Conditioning',
    'member_since' => $_SESSION['member_since'] ?? date('F Y', strtotime('-1 year')),
    'stats' => [
        'classes_this_week' => $_SESSION['stats']['classes_this_week'] ?? 12,
        'clients' => $_SESSION['stats']['clients'] ?? 28,
        'rating' => $_SESSION['stats']['rating'] ?? 4.8,
        'hours_this_week' => $_SESSION['stats']['hours_this_week'] ?? 18
    ],
    'upcoming_classes' => $_SESSION['upcoming_classes'] ?? [
        ['name' => 'Advanced HIIT', 'time' => 'Today, 8:00 AM', 'location' => 'Studio A', 'attendees' => 12],
        ['name' => 'Personal Training', 'time' => 'Today, 2:00 PM', 'location' => 'PT Area', 'attendees' => 1],
        ['name' => 'Power Yoga', 'time' => 'Tomorrow, 7:00 AM', 'location' => 'Studio B', 'attendees' => 15]
    ],
    'client_progress' => $_SESSION['client_progress'] ?? [
        ['name' => 'Sarah Johnson', 'goal' => 'Weight Loss', 'progress' => 75, 'trend' => 'up'],
        ['name' => 'Michael Brown', 'goal' => 'Strength Gain', 'progress' => 62, 'trend' => 'up'],
        ['name' => 'Emma Wilson', 'goal' => 'Marathon Prep', 'progress' => 88, 'trend' => 'steady']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - FitZone</title>
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
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
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

        .trainer-specialization {
            display: inline-block;
            background-color: rgba(164, 0, 0, 0.2);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 0.9rem;
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
            color: var(--success);
        }

        /* Upcoming Classes */
        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 2rem;
            color: white;
            margin: 30px 0 20px;
            letter-spacing: 1px;
        }

        .classes-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .classes-table th {
            background-color: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .classes-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
        }

        .classes-table tr:last-child td {
            border-bottom: none;
        }

        .classes-table tr:hover {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .class-name {
            font-weight: 700;
            color: white;
        }

        .class-time {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .class-location {
            display: inline-block;
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .class-attendees {
            color: var(--text-secondary);
        }

        /* Client Progress */
        .progress-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .client-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(164, 0, 0, 0.3);
        }

        .client-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .client-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }

        .client-info {
            flex: 1;
        }

        .client-name {
            font-weight: 700;
            color: white;
            margin-bottom: 3px;
        }

        .client-goal {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .progress-bar {
            height: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            margin: 15px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary);
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .progress-percent {
            text-align: right;
            font-weight: 700;
            color: white;
        }

        .progress-trend {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .progress-trend i {
            margin-right: 5px;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-btn {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            border: 2px solid var(--primary);
        }

        .action-btn:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .action-btn:hover .action-icon {
            color: white;
        }

        .action-text {
            font-weight: 700;
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

            .progress-cards {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .classes-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="dashboard-header">
        <a href="staff_dashboard.php" class="logo">FitZone <span>Trainer</span></a>
        <nav class="user-nav">
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($trainerData['fullname']) ?>&background=random" alt="Trainer">
                <span class="user-name"><?= htmlspecialchars($trainerData['fullname']) ?></span>
            </div>
            <a href="/fitzone/logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-menu">
                <a href="staff_dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="schedule.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i>
                    My Schedule
                </a>
                <a href="clients.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    My Clients
                </a>
                <a href="queries.php" class="menu-item">
                    <i class="fas fa-dumbbell"></i>
                    Customer Queries
                </a>
                <a href="bookings.php" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    Manage Booking
                </a>
                <a href="profile.php" class="menu-item">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <section class="welcome-section">
                <h1 class="welcome-title">Welcome, <?= htmlspecialchars($trainerData['fullname']) ?></h1>
                <p class="welcome-subtitle">Trainer Dashboard - Track your classes and clients</p>
                <span class="trainer-specialization">
                    <i class="fas fa-certificate"></i> <?= $trainerData['specialization'] ?>
                </span>
            </section>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Classes This Week</h3>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $trainerData['stats']['classes_this_week'] ?></div>
                    <div class="stat-change positive">+2 from last week</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Active Clients</h3>
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $trainerData['stats']['clients'] ?></div>
                    <div class="stat-change">4 new this month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Average Rating</h3>
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $trainerData['stats']['rating'] ?></div>
                    <div class="stat-change positive">/5 from 84 reviews</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Hours This Week</h3>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $trainerData['stats']['hours_this_week'] ?></div>
                    <div class="stat-change">hours scheduled</div>
                </div>
            </div>

            <!-- Upcoming Classes -->
            <h2 class="section-title">Upcoming Classes</h2>
            <table class="classes-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Attendees</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainerData['upcoming_classes'] as $class): ?>
                        <tr>
                            <td>
                                <div class="class-name"><?= $class['name'] ?></div>
                                <div class="class-time"><?= $class['time'] ?></div>
                            </td>
                            <td><?= $class['time'] ?></td>
                            <td><span class="class-location"><?= $class['location'] ?></span></td>
                            <td><span class="class-attendees"><?= $class['attendees'] ?> registered</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <!-- Quick Actions -->
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-actions">
                <a href="add_session.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-text">Add Session</div>
                </a>
                <a href="log_progress.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="action-text">Log Progress</div>
                </a>
                <a href="messages.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="action-text">Messages</div>
                </a>
                <a href="resources.php" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="action-text">Training Resources</div>
                </a>
            </div>
        </main>
    </div>
</body>
</html>