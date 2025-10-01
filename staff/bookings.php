<?php
// staff/bookings.php
session_start();

// Check if user is logged in and is staff
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'trainer') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../db_connect.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type'])) {
        $session_id = $_POST['session_id'];
        $staff_id = $_SESSION['user_id'];
        $action_type = $_POST['action_type'];
        $notes = $_POST['notes'] ?? '';

        try {
            // Record action in booking_actions
            $stmt = $conn->prepare("
                INSERT INTO booking_actions 
                (session_id, staff_id, action_type, notes) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiss", $session_id, $staff_id, $action_type, $notes);
            $stmt->execute();

            // Update training session if needed
            if ($action_type === 'resolve') {
                $update_stmt = $conn->prepare("
                    UPDATE training_sessions 
                    SET status = 'resolved', last_updated = NOW() 
                    WHERE id = ?
                ");
                $update_stmt->bind_param("i", $session_id);
                $update_stmt->execute();
                $_SESSION['message'] = "Session resolved successfully!";
            }

            if ($action_type === 'reply') {
                $_SESSION['message'] = "Note added successfully!";
            }

            header("Location: bookings.php");
            exit();

        } catch(Exception $e) {
            $_SESSION['error'] = "Error processing action: " . $e->getMessage();
            header("Location: bookings.php");
            exit();
        }
    }
}

// Fetch all training sessions
$sessions = [];
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$query = "SELECT ts.*, u.fullname AS member_name 
          FROM training_sessions ts
          JOIN users u ON ts.member_id = u.id
          WHERE 1=1";

$params = [];
$types = '';

// Apply filters
if ($status_filter !== 'all') {
    $query .= " AND ts.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($search)) {
    $query .= " AND (u.fullname LIKE ? OR ts.session_type LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$query .= " ORDER BY ts.session_date DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}

// Sample trainer data
$trainerData = [
    'fullname' => $_SESSION['fullname'] ?? 'Trainer',
    'specialization' => $_SESSION['specialization'] ?? 'Strength & Conditioning'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - FitZone</title>
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
            <div class="page-header">
                <div>
                    <h1 class="page-title">Training Sessions</h1>
                    <p class="page-subtitle">Manage and update training sessions</p>
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
                        <option value="scheduled" <?= $status_filter === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>

                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" name="search" placeholder="Search sessions..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>

            <!-- Sessions Table -->
            <table class="queries-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Session Type</th>
                        <th>Date/Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td>
                                <div class="query-member">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($session['member_name']) ?>&background=random" class="member-avatar" alt="Member">
                                    <span class="member-name"><?= htmlspecialchars($session['member_name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($session['session_type']) ?></td>
                            <td>
                                <div class="query-date"><?= date('M j, Y g:i A', strtotime($session['session_date'] . ' ' . $session['session_time'])) ?></div>
                            </td>
                            <td>
                                <span class="query-status status-<?= $session['status'] ?>">
                                    <?= ucfirst($session['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view view-session" 
                                        data-id="<?= $session['id'] ?>"
                                        data-member="<?= htmlspecialchars($session['member_name']) ?>"
                                        data-type="<?= htmlspecialchars($session['session_type']) ?>"
                                        data-date="<?= date('M j, Y g:i A', strtotime($session['session_date'] . ' ' . $session['session_time'])) ?>"
                                        data-status="<?= $session['status'] ?>"
                                        data-notes="<?= htmlspecialchars($session['notes'] ?? '') ?>">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    
                                    <?php if ($session['status'] !== 'resolved'): ?>
                                        <button class="btn-action btn-reply reply-session" data-id="<?= $session['id'] ?>">
                                            <i class="fas fa-reply"></i> Add Note
                                        </button>
                                        
                                        <button class="btn-action btn-resolve resolve-session" data-id="<?= $session['id'] ?>">
                                            <i class="fas fa-check"></i> Resolve
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Session Detail Modal -->
            <div class="modal-overlay" id="sessionModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3 class="modal-title">Session Details</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="query-detail-header">
                            <img src="" class="query-detail-avatar" alt="Member" id="sessionAvatar">
                            <div class="query-detail-info">
                                <div class="query-detail-name" id="sessionMember"></div>
                                <div class="query-detail-meta">
                                    <div class="query-detail-date" id="sessionDate"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="query-detail-subject" id="sessionType"></div>
                        <div class="query-detail-message" id="sessionNotes"></div>

                        <!-- Note Form -->
                        <form method="POST" class="reply-form" id="noteForm">
                            <input type="hidden" name="session_id" id="noteSessionId">
                            <input type="hidden" name="action_type" value="reply">
                            <div class="reply-title">Add Session Note</div>
                            <textarea name="notes" placeholder="Enter session notes..." required></textarea>
                            <div class="form-actions">
                                <button type="button" class="btn-action btn-view close-note">Cancel</button>
                                <button type="submit" class="btn-primary">Save Note</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('sessionModal');
        const viewButtons = document.querySelectorAll('.view-session');
        const closeModal = document.querySelector('.close-modal');

        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('sessionMember').textContent = button.getAttribute('data-member');
                document.getElementById('sessionAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(button.getAttribute('data-member'))}&background=random`;
                document.getElementById('sessionDate').textContent = button.getAttribute('data-date');
                document.getElementById('sessionType').textContent = button.getAttribute('data-type');
                document.getElementById('sessionNotes').textContent = button.getAttribute('data-notes') || 'No notes available';
                
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Handle note submissions
        document.querySelectorAll('.reply-session').forEach(button => {
            button.addEventListener('click', () => {
                const sessionId = button.getAttribute('data-id');
                document.getElementById('noteSessionId').value = sessionId;
                modal.classList.add('active');
                document.getElementById('noteForm').style.display = 'block';
            });
        });

        // Handle resolve actions
        document.querySelectorAll('.resolve-session').forEach(button => {
            button.addEventListener('click', () => {
                if (confirm('Are you sure you want to resolve this session?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    const sessionId = document.createElement('input');
                    sessionId.type = 'hidden';
                    sessionId.name = 'session_id';
                    sessionId.value = button.getAttribute('data-id');
                    
                    const actionType = document.createElement('input');
                    actionType.type = 'hidden';
                    actionType.name = 'action_type';
                    actionType.value = 'resolve';
                    
                    form.appendChild(sessionId);
                    form.appendChild(actionType);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Close modal
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    </script>
</body>
</html>