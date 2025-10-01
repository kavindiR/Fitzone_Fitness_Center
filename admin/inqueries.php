<?php
// admin/inqueries.php
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

require_once __DIR__ . '/../db_connect.php';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $inquiry_id = $conn->real_escape_string($_POST['inquiry_id']);
    $reply = $conn->real_escape_string(trim($_POST['reply']));
    $status = $conn->real_escape_string($_POST['status']);

    // Update inquiry with reply and status
    $stmt = $conn->prepare("UPDATE inquiries SET replies = CONCAT(IFNULL(replies, ''), '\n', ?), status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $reply, $status, $inquiry_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all inquiries with user details
$query = "
    SELECT i.*, u.fullname AS user_name 
    FROM inquiries i 
    LEFT JOIN users u ON i.user_id = u.id 
    ORDER BY i.created_at DESC
";
$result = $conn->query($query);
$inquiries = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .inquiry-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            border-top: 3px solid var(--primary);
        }

        .inquiry-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(164, 0, 0, 0.3);
        }

        .inquiry-header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
        }

        .inquiry-header h3 {
            font-family: 'Anton', sans-serif;
            font-size: 1.5rem;
            color: var(--primary);
            flex: 1 1 100%;
        }

        .inquiry-meta {
            display: flex;
            gap: 20px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .status-new { background-color: rgba(76, 201, 240, 0.2); color: var(--info); }
        .status-replied { background-color: rgba(248, 150, 30, 0.2); color: var(--warning); }
        .status-resolved { background-color: rgba(82, 183, 136, 0.2); color: var(--success); }
        .status-pending { background-color: rgba(249, 65, 68, 0.2); color: var(--danger); }

        .inquiry-message {
            line-height: 1.6;
            padding: 15px;
            background-color: #111;
            border-radius: 8px;
            margin: 15px 0;
        }

        .replies-box {
            background-color: #111;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 3px solid var(--primary);
        }

        .reply-form {
            margin-top: 25px;
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }

        .reply-form textarea {
            width: 100%;
            padding: 15px;
            background-color: #111;
            color: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin: 10px 0;
            min-height: 120px;
            resize: vertical;
        }

        .reply-form select {
            padding: 10px 15px;
            background-color: #111;
            color: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin: 10px 0;
            width: 200px;
        }

        .btn-reply {
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn-reply:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .reply-divider {
            margin: 25px 0;
            border-color: var(--border);
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .inquiry-header {
                flex-direction: column;
            }
            
            .reply-form select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <a href="admin_dashboard.php" class="logo">FitZone <span>Fitness</span></a>
        <nav class="user-nav">
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['fullname']) ?>&background=random" alt="Admin">
                <span class="user-name"><?= htmlspecialchars($_SESSION['fullname']) ?></span>
            </div>
            <a href="/fitzone/logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-menu">
                <a href="admin_dashboard.php" class="menu-item">
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
                
                <a href="inqueries.php" class="menu-item active">
                    <i class="fas fa-question-circle"></i>
                    Inquiries
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <section class="welcome-section">
                <h1 class="welcome-title">Manage Inquiries</h1>
                <p class="welcome-subtitle">View and respond to member inquiries</p>
            </section>

            <?php foreach ($inquiries as $inquiry): ?>
                <div class="inquiry-container">
                    <div class="inquiry-header">
                        <h3><?= htmlspecialchars($inquiry['subject']) ?></h3>
                        <div class="inquiry-meta">
                            <div>
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($inquiry['name']) ?>
                                <?php if ($inquiry['user_name']): ?>
                                    (Member: <?= htmlspecialchars($inquiry['user_name']) ?>)
                                <?php endif; ?>
                            </div>
                            <div>
                                <i class="fas fa-clock"></i>
                                <?= date('M j, Y g:i a', strtotime($inquiry['created_at'])) ?>
                            </div>
                            <div class="status-badge status-<?= $inquiry['status'] ?>">
                                <?= ucfirst($inquiry['status']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="inquiry-message">
                        <?= nl2br(htmlspecialchars($inquiry['message'])) ?>
                    </div>

                    <?php if ($inquiry['replies']): ?>
                        <div class="replies-box">
                            <h4><i class="fas fa-comments"></i> Previous Responses</h4>
                            <hr class="reply-divider">
                            <?= nl2br(htmlspecialchars($inquiry['replies'])) ?>
                        </div>
                    <?php endif; ?>

                    <form class="reply-form" method="POST">
                        <input type="hidden" name="inquiry_id" value="<?= $inquiry['id'] ?>">
                        
                        <textarea 
                            name="reply" 
                            placeholder="Type your response here..."
                            required
                        ></textarea>

                        <div class="form-footer">
                            <select name="status" required>
                                <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                <option value="replied" <?= $inquiry['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                                <option value="resolved" <?= $inquiry['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="pending" <?= $inquiry['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            </select>
                            
                            <button type="submit" class="btn-reply">
                                <i class="fas fa-paper-plane"></i> Submit Response
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </main>
    </div>
</body>
</html>