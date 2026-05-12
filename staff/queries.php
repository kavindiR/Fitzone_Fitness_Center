<?php
// staff/queries.php
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

require_once __DIR__ . '/../db_connect.php';

// Sample trainer data
$trainerData = [
    'fullname' => $_SESSION['fullname'] ?? 'Trainer',
    'specialization' => $_SESSION['specialization'] ?? 'Strength & Conditioning'
];

// Handle status updates and replies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $query_id = $_POST['query_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $query_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = "Status updated successfully!";
        header("Location: queries.php");
        exit();
    }
    
    if (isset($_POST['send_reply'])) {
        $query_id = $_POST['query_id'];
        $reply_message = $_POST['reply_message'];
        
        // Update status and store the reply
        $stmt = $conn->prepare("UPDATE inquiries SET status = 'replied', replies = ? WHERE id = ?");
        $stmt->bind_param("si", $reply_message, $query_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = "Reply sent successfully!";
        header("Location: queries.php");
        exit();
    }
}

// Fetch all inquiries
$queries = [];
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$query = "SELECT inquiries.*, users.fullname AS member_name 
          FROM inquiries 
          LEFT JOIN users ON inquiries.user_id = users.id 
          WHERE 1=1";

$params = [];
$types = '';

if ($status_filter !== 'all') {
    $query .= " AND inquiries.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($search)) {
    $query .= " AND (inquiries.subject LIKE ? OR inquiries.message LIKE ? OR inquiries.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

$query .= " ORDER BY inquiries.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Set priority based on age of query
    $row['priority'] = 'medium';
    if (strtotime($row['created_at']) > strtotime('-1 day')) {
        $row['priority'] = 'high';
    } elseif (strtotime($row['created_at']) < strtotime('-3 days')) {
        $row['priority'] = 'low';
    }
    
    $queries[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Queries - FitZone</title>
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-family: 'Anton', sans-serif;
            font-size: 2.5rem;
            color: white;
            letter-spacing: 1px;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Message Alert */
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

        /* Query Filters */
        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .filter-select {
            background-color: var(--card-bg);
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .search-box {
            flex: 1;
            max-width: 300px;
            position: relative;
        }

        .search-input {
            width: 100%;
            background-color: var(--card-bg);
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 15px 8px 40px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        /* Queries Table */
        .queries-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .queries-table th {
            background-color: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .queries-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
        }

        .queries-table tr:last-child td {
            border-bottom: none;
        }

        .queries-table tr:hover {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .query-member {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .member-name {
            font-weight: 700;
            color: white;
        }

        .query-subject {
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .query-message {
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .query-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .query-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-pending {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning);
        }

        .status-replied {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }

        .status-resolved {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .priority-high {
            color: var(--danger);
            font-weight: 700;
        }

        .priority-medium {
            color: var(--warning);
            font-weight: 700;
        }

        .priority-low {
            color: var(--success);
            font-weight: 700;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-view {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }

        .btn-view:hover {
            background-color: var(--success);
            color: white;
        }

        .btn-reply {
            background-color: rgba(164, 0, 0, 0.2);
            color: var(--primary);
        }

        .btn-reply:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-resolve {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .btn-resolve:hover {
            background-color: #28a745;
            color: white;
        }

        /* Query Detail Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background-color: var(--card-bg);
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-50px);
            transition: all 0.3s;
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .close-modal:hover {
            color: var(--primary);
        }

        .modal-body {
            padding: 20px;
        }

        .query-detail-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .query-detail-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }

        .query-detail-info {
            flex: 1;
        }

        .query-detail-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            margin-bottom: 5px;
        }

        .query-detail-meta {
            display: flex;
            gap: 15px;
        }

        .query-detail-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .query-detail-priority {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .query-detail-subject {
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
        }

        .query-detail-message {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Replies Section */
        .replies-section {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 3px solid var(--primary);
        }

        .reply-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
        }

        .reply-message {
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Attachment Section */
        .attachment-section {
            margin-bottom: 20px;
        }

        .attachment-link {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .attachment-link:hover {
            text-decoration: underline;
        }

        /* Reply Form */
        .reply-form {
            margin-top: 30px;
        }

        .reply-form textarea {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 15px;
            border-radius: 10px;
            min-height: 150px;
            margin-bottom: 15px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        /* Status Selector */
        .status-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .status-btn {
            padding: 5px 15px;
            border-radius: 15px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.2s;
        }

        .status-btn:hover {
            opacity: 0.8;
        }

        .status-btn.pending {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning);
        }

        .status-btn.replied {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }

        .status-btn.resolved {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
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

            .filter-bar {
                flex-direction: column;
            }

            .search-box {
                max-width: 100%;
            }

            .queries-table {
                display: block;
                overflow-x: auto;
            }

            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }

            .btn-action {
                width: 100%;
                text-align: center;
            }

            .status-selector {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .query-detail-meta {
                flex-direction: column;
                gap: 5px;
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
                <a href="staff_dashboard.php" class="menu-item">
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
                <a href="queries.php" class="menu-item active">
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
            <div class="page-header">
                <div>
                    <h1 class="page-title">Customer Queries</h1>
                    <p class="page-subtitle">Manage and respond to member inquiries</p>
                </div>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <!-- Filter Bar -->
            <form method="GET" class="filter-bar">
                <div class="filter-group">
                    <span class="filter-label">Status:</span>
                    <select class="filter-select" name="status" onchange="this.form.submit()">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Replied</option>
                        <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" name="search" placeholder="Search queries..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn-primary" style="display: none;">Search</button>
            </form>

            <!-- Queries Table -->
            <table class="queries-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Query</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queries as $query): ?>
                        <tr>
                            <td>
                                <div class="query-member">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($query['name'] ?? 'Unknown') ?>&background=random" class="member-avatar" alt="Member">
                                    <span class="member-name"><?= htmlspecialchars($query['name'] ?? 'Guest') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="query-subject"><?= htmlspecialchars($query['subject']) ?></div>
                                <div class="query-message"><?= htmlspecialchars($query['message']) ?></div>
                            </td>
                            <td>
                                <div class="query-date"><?= date('M j, Y g:i A', strtotime($query['created_at'])) ?></div>
                            </td>
                            <td>
                                <span class="query-status status-<?= $query['status'] ?>">
                                    <?= ucfirst($query['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-<?= $query['priority'] ?>">
                                    <?= ucfirst($query['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view view-query" data-id="<?= $query['id'] ?>" 
                                        data-name="<?= htmlspecialchars($query['name'] ?? 'Guest') ?>"
                                        data-email="<?= htmlspecialchars($query['email']) ?>"
                                        data-subject="<?= htmlspecialchars($query['subject']) ?>"
                                        data-message="<?= htmlspecialchars($query['message']) ?>"
                                        data-date="<?= date('M j, Y g:i A', strtotime($query['created_at'])) ?>"
                                        data-status="<?= $query['status'] ?>"
                                        data-priority="<?= $query['priority'] ?>"
                                        data-file-path="<?= htmlspecialchars($query['file_path'] ?? '') ?>"
                                        data-replies="<?= htmlspecialchars($query['replies'] ?? '') ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if ($query['status'] !== 'resolved'): ?>
                                        <button class="btn-action btn-reply reply-query" data-id="<?= $query['id'] ?>">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($query['status'] !== 'resolved'): ?>
                                        <button class="btn-action btn-resolve resolve-query" data-id="<?= $query['id'] ?>">
                                            <i class="fas fa-check"></i> Resolve
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Query Detail Modal -->
            <div class="modal-overlay" id="queryModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3 class="modal-title">Query Details</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="query-detail-header">
                            <img src="" class="query-detail-avatar" alt="Member" id="detailAvatar">
                            <div class="query-detail-info">
                                <div class="query-detail-name" id="detailName"></div>
                                <div class="query-detail-meta">
                                    <div class="query-detail-date" id="detailDate"></div>
                                    <div class="query-detail-priority" id="detailPriority"></div>
                                </div>
                            </div>
                        </div>
                        <div class="query-detail-subject" id="detailSubject"></div>
                        <div class="query-detail-message" id="detailMessage"></div>
                        
                        <!-- Replies Section -->
                        <div class="replies-section" id="repliesSection">
                            <div class="reply-title">Your Reply</div>
                            <div class="reply-message" id="replyMessage"></div>
                        </div>
                        
                        <!-- Attachment Section -->
                        <div class="attachment-section" id="attachmentSection" style="display: none;">
                            <div class="reply-title">Attachment</div>
                            <a href="#" class="attachment-link" id="attachmentLink" target="_blank">
                                <i class="fas fa-paperclip"></i> View Attachment
                            </a>
                        </div>
                        
                        <!-- Status Selector -->
                        <form method="POST" class="status-selector" id="statusForm">
                            <input type="hidden" name="query_id" id="statusQueryId">
                            <input type="hidden" name="update_status" value="1">
                            <button type="submit" name="new_status" value="pending" class="status-btn pending">Mark as Pending</button>
                            <button type="submit" name="new_status" value="replied" class="status-btn replied">Mark as Replied</button>
                            <button type="submit" name="new_status" value="resolved" class="status-btn resolved">Mark as Resolved</button>
                        </form>
                        
                        <!-- Reply Form -->
                        <form method="POST" class="reply-form" id="replyForm">
                            <input type="hidden" name="query_id" id="replyQueryId">
                            <input type="hidden" name="send_reply" value="1">
                            <div class="reply-title">Add Reply</div>
                            <textarea name="reply_message" placeholder="Type your reply here..." required></textarea>
                            <div class="form-actions">
                                <button type="button" class="btn-action btn-view close-reply">Cancel</button>
                                <button type="submit" class="btn-primary">Send Reply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('queryModal');
        const viewButtons = document.querySelectorAll('.view-query');
        const closeModal = document.querySelector('.close-modal');
        const closeReply = document.querySelector('.close-reply');
        const statusForm = document.getElementById('statusForm');
        const replyForm = document.getElementById('replyForm');
        const repliesSection = document.getElementById('repliesSection');
        const replyMessage = document.getElementById('replyMessage');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Populate modal with data
                document.getElementById('detailName').textContent = button.getAttribute('data-name');
                document.getElementById('detailAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(button.getAttribute('data-name'))}&background=random`;
                document.getElementById('detailDate').textContent = button.getAttribute('data-date');
                document.getElementById('detailSubject').textContent = button.getAttribute('data-subject');
                document.getElementById('detailMessage').textContent = button.getAttribute('data-message');
                document.getElementById('detailPriority').textContent = button.getAttribute('data-priority');
                document.getElementById('statusQueryId').value = button.getAttribute('data-id');
                document.getElementById('replyQueryId').value = button.getAttribute('data-id');
                
                // Handle replies
                const replies = button.getAttribute('data-replies');
                if (replies && replies.trim() !== '') {
                    replyMessage.textContent = replies;
                    repliesSection.style.display = 'block';
                } else {
                    repliesSection.style.display = 'none';
                }
                
                // Handle attachment
                const filePath = button.getAttribute('data-file-path');
                const attachmentSection = document.getElementById('attachmentSection');
                const attachmentLink = document.getElementById('attachmentLink');
                
                if (filePath && filePath.trim() !== '') {
                    attachmentSection.style.display = 'block';
                    attachmentLink.href = filePath;
                } else {
                    attachmentSection.style.display = 'none';
                }
                
                // Show the modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Hide reply form by default
                replyForm.style.display = 'none';
                statusForm.style.display = 'block';
            });
        });
        
        // Reply button functionality
        document.querySelectorAll('.reply-query').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const queryId = button.getAttribute('data-id');
                
                // Show the modal first
                const row = button.closest('tr');
                const viewButton = row.querySelector('.view-query');
                viewButton.click();
                
                // Then show the reply form and hide status form
                replyForm.style.display = 'block';
                statusForm.style.display = 'none';
            });
        });
        
        // Resolve button functionality
        document.querySelectorAll('.resolve-query').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const queryId = button.getAttribute('data-id');
                
                if (confirm('Mark this query as resolved?')) {
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    const queryIdInput = document.createElement('input');
                    queryIdInput.type = 'hidden';
                    queryIdInput.name = 'query_id';
                    queryIdInput.value = queryId;
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'new_status';
                    statusInput.value = 'resolved';
                    
                    const updateInput = document.createElement('input');
                    updateInput.type = 'hidden';
                    updateInput.name = 'update_status';
                    updateInput.value = '1';
                    
                    form.appendChild(queryIdInput);
                    form.appendChild(statusInput);
                    form.appendChild(updateInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
        
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        
        closeReply.addEventListener('click', () => {
            replyForm.style.display = 'none';
            statusForm.style.display = 'block';
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>