<?php
// admin/admin_dashboard.php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Sample admin data - in a real app this would come from your database
$adminData = [
    'fullname' => $_SESSION['fullname'] ?? 'Admin',
    'last_login' => $_SESSION['last_login'] ?? date('F j, Y, g:i a', strtotime('-1 hour')),
    'admin_since' => $_SESSION['admin_since'] ?? date('F Y', strtotime('-6 months')),
    'privileges' => $_SESSION['privileges'] ?? ['User Management', 'Content Management', 'System Settings']
];

// Sample system stats - would come from database in real app
$systemStats = [
    'total_members' => 1245,
    'active_members' => 892,
    'new_this_month' => 87,
    'classes_scheduled' => 56,
    'trainers' => 12,
    'revenue' => 45280
];

// Recent activity log
$activityLog = [
    [
        'time' => '10 minutes ago',
        'action' => 'Updated class schedule',
        'user' => 'John Smith',
        'icon' => 'fa-calendar-alt',
        'color' => 'text-info'
    ],
    [
        'time' => '25 minutes ago',
        'action' => 'Approved new member registration',
        'user' => 'Sarah Johnson',
        'icon' => 'fa-user-plus',
        'color' => 'text-success'
    ],
    [
        'time' => '1 hour ago',
        'action' => 'Processed membership payment',
        'user' => 'Michael Brown',
        'icon' => 'fa-credit-card',
        'color' => 'text-warning'
    ],
    [
        'time' => '2 hours ago',
        'action' => 'Reset password for user',
        'user' => 'Emily Davis',
        'icon' => 'fa-key',
        'color' => 'text-danger'
    ],
    [
        'time' => '3 hours ago',
        'action' => 'Updated system settings',
        'user' => 'Admin',
        'icon' => 'fa-cog',
        'color' => 'text-primary'
    ]
];

// Recent members
$recentMembers = [
    [
        'name' => 'Alex Turner',
        'join_date' => 'Today',
        'plan' => 'Premium',
        'status' => 'active',
        'photo' => 'https://randomuser.me/api/portraits/men/32.jpg'
    ],
    [
        'name' => 'Maria Garcia',
        'join_date' => 'Yesterday',
        'plan' => 'Elite',
        'status' => 'active',
        'photo' => 'https://randomuser.me/api/portraits/women/44.jpg'
    ],
    [
        'name' => 'James Wilson',
        'join_date' => '2 days ago',
        'plan' => 'Basic',
        'status' => 'pending',
        'photo' => 'https://randomuser.me/api/portraits/men/65.jpg'
    ],
    [
        'name' => 'Sophie Martin',
        'join_date' => '3 days ago',
        'plan' => 'Premium',
        'status' => 'active',
        'photo' => 'https://randomuser.me/api/portraits/women/68.jpg'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitZone</title>
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
            --info: #4cc9f0;
            --success: #52b788;
            --warning: #f8961e;
            --danger: #f94144;
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

        /* Admin Info Card */
        .admin-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-top: 5px solid var(--primary);
        }

        .admin-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .admin-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 15px;
        }

        .admin-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.5rem;
            color: white;
            letter-spacing: 1px;
        }

        .admin-since {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .privileges-list {
            margin-top: 20px;
        }

        .privilege-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .privilege-item:last-child {
            border-bottom: none;
        }

        .privilege-item i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Activity Log */
        .activity-log {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 2rem;
            color: white;
            margin: 30px 0 20px;
            letter-spacing: 1px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .activity-details {
            flex: 1;
        }

        .activity-action {
            font-weight: 700;
            color: white;
        }

        .activity-time {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 3px;
        }

        .activity-user {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Recent Members */
        .recent-members {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .member-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }

        .member-item:last-child {
            border-bottom: none;
        }

        .member-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            font-weight: 700;
            color: white;
        }

        .member-details {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 3px;
        }

        .member-plan {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .plan-basic {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--info);
        }

        .plan-premium {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning);
        }

        .plan-elite {
            background-color: rgba(82, 183, 136, 0.2);
            color: var(--success);
        }

        .member-status {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-active {
            background-color: rgba(82, 183, 136, 0.2);
            color: var(--success);
        }

        .status-pending {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(164, 0, 0, 0.3);
        }

        .action-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .action-title {
            font-weight: 700;
            color: white;
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

            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 576px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header Matching index.php -->
    <header class="dashboard-header">
        <a href="admin_dashboard.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="user-nav">
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminData['fullname']) ?>&background=random" alt="Admin">
                <span class="user-name"><?= htmlspecialchars($adminData['fullname']) ?></span>
            </div>
            <a href="/fitzone/logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-menu">
                <a href="admin_dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="members.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    Members
                </a>
                <a href="trainers.php" class="menu-item">
                    <i class="fas fa-user-tie"></i>
                    Trainers
                </a>
                <a href="classes.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i>
                    Classes
                </a>
                
                <a href="inqueries.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    Inqueries
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <section class="welcome-section">
                <h1 class="welcome-title">Admin Dashboard</h1>
                <p class="welcome-subtitle">Welcome back, <?= htmlspecialchars($adminData['fullname']) ?>. Last login: <?= $adminData['last_login'] ?></p>
            </section>

            <!-- Admin Info -->
            <div class="admin-card">
                <div class="admin-header">
                    <i class="fas fa-shield-alt admin-icon"></i>
                    <div>
                        <h2 class="admin-title">Administrator</h2>
                        <p class="admin-since">Admin since <?= $adminData['admin_since'] ?></p>
                    </div>
                </div>
                <div class="privileges-list">
                    <h3 class="section-title">Your Privileges:</h3>
                    <?php foreach ($adminData['privileges'] as $privilege): ?>
                        <div class="privilege-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?= $privilege ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Total Members</h3>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($systemStats['total_members']) ?></div>
                    <div class="stat-change positive">+<?= $systemStats['new_this_month'] ?> this month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Active Members</h3>
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($systemStats['active_members']) ?></div>
                    <div class="stat-change positive"><?= round(($systemStats['active_members']/$systemStats['total_members'])*100) ?>% active rate</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Classes Scheduled</h3>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= $systemStats['classes_scheduled'] ?></div>
                    <div class="stat-change positive">This week</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Monthly Revenue</h3>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="stat-value">Rs<?= number_format($systemStats['revenue']) ?></div>
                    <div class="stat-change positive">+8% from last month</div>
                </div>
            </div>

            

            <!-- Quick Actions -->
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-actions">
                <div class="action-card" onclick="window.location.href='members.php?action=add'">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="action-title">Add New Member</h3>
                </div>
                <div class="action-card" onclick="window.location.href='classes.php?action=add'">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3 class="action-title">Schedule Class</h3>
                </div>
                <div class="action-card" onclick="window.location.href='payments.php'">
                    <div class="action-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="action-title">Process Payment</h3>
                </div>
                <div class="action-card" onclick="window.location.href='reports.php'">
                    <div class="action-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="action-title">Generate Report</h3>
                </div>
            </div>
        </main>
    </div>
</body>
</html>