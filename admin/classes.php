<?php
// admin/classes.php
session_start();
require_once '../db_connect.php';

// Check admin authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete Class
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    
    // Get image path
    $result = $conn->query("SELECT image FROM classes WHERE id = $id");
    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
        $image_path = "../uploads/classes/" . $class['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $conn->query("DELETE FROM classes WHERE id = $id");
    $_SESSION['message'] = "Class deleted successfully!";
    header("Location: classes.php");
    exit();
}

// Handle Add/Edit Class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $schedule = $conn->real_escape_string($_POST['schedule']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $trainer_id = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : null;

    // File upload handling
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/classes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image = $file_name;
        } else {
            $_SESSION['error'] = "Failed to upload image";
            header("Location: classes.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please select a class image";
        header("Location: classes.php");
        exit();
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO classes (name, description, image, schedule, duration, trainer_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $description, $image, $schedule, $duration, $trainer_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Class added successfully!";
    } else {
        $_SESSION['error'] = "Error adding class: " . $conn->error;
    }
    
    header("Location: classes.php");
    exit();
}

// Get all classes with trainer information
$classes = $conn->query("
    SELECT c.*, t.name AS trainer_name 
    FROM classes c
    LEFT JOIN trainers t ON c.trainer_id = t.id
    ORDER BY c.created_at DESC
");

// Get all trainers for dropdown
$trainers = $conn->query("SELECT id, name FROM trainers ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - FitZone Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
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

        /* Header - Matching admin_dashboard.php */
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

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: #444;
        }

        /* Messages */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 700;
        }

        .message.success {
            background-color: rgba(82, 183, 136, 0.2);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .message.error {
            background-color: rgba(249, 65, 68, 0.2);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        /* Members Table */
        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .members-table th, .members-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .members-table th {
            background-color: var(--primary);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .members-table tr:hover {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-active {
            background-color: rgba(82, 183, 136, 0.2);
            color: var(--success);
        }

        .status-inactive {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning);
        }

        .status-suspended {
            background-color: rgba(249, 65, 68, 0.2);
            color: var(--danger);
        }

        .action-links a {
            color: var(--text-secondary);
            margin-right: 10px;
            transition: color 0.3s;
        }

        .action-links a:hover {
            color: var(--primary);
        }

        /* Member Form */
        .member-form {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            background-color: #333;
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
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

            .members-table {
                display: block;
                overflow-x: auto;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
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

        /* Header - Matching admin_dashboard.php */
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

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: #444;
        }

        /* Messages */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 700;
        }

        .message.success {
            background-color: rgba(82, 183, 136, 0.2);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .message.error {
            background-color: rgba(249, 65, 68, 0.2);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        /* Trainers Table */
        .trainers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .trainers-table th, .trainers-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .trainers-table th {
            background-color: var(--primary);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .trainers-table tr:hover {
            background-color: rgba(164, 0, 0, 0.1);
        }

        .trainer-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .action-links a {
            color: var(--text-secondary);
            margin-right: 10px;
            transition: color 0.3s;
        }

        .action-links a:hover {
            color: var(--primary);
        }

        /* Trainer Form */
        .trainer-form {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            background-color: #333;
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        /* File Upload Styles */
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-label {
            display: block;
            padding: 10px 15px;
            background-color: #333;
            border: 1px solid var(--border);
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }
        
        .file-upload-label:hover {
            background-color: #444;
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-name {
            margin-top: 5px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            display: none;
            border-radius: 5px;
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

            .trainers-table {
                display: block;
                overflow-x: auto;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

    </style>
</head>
<body>
    <!-- Header Matching admin_dashboard.php -->
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
        <!-- Sidebar -->
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
                <a href="classes.php" class="menu-item active">
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
            <div class="page-header">
                <h1 class="page-title">Manage Classes</h1>
                <a href="#add-class-form" class="btn">
                    <i class="fas fa-plus"></i> Add New Class
                </a>
            </div>

            <!-- Display messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success">
                    <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Add Class Form -->
            <div id="add-class-form" class="trainer-form">
                <h2 style="margin-bottom: 20px; color: var(--primary);">Add New Class</h2>
                <form method="POST" action="classes.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Class Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="schedule">Schedule *</label>
                            <input type="text" id="schedule" name="schedule" class="form-control" 
                                   placeholder="e.g., Mon/Wed/Fri 5:00 PM" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">Duration *</label>
                            <input type="text" id="duration" name="duration" class="form-control" 
                                   placeholder="e.g., 60 minutes" required>
                        </div>
                        <div class="form-group">
                            <label for="trainer_id">Trainer</label>
                            <select id="trainer_id" name="trainer_id" class="form-control">
                                <option value="">Select Trainer</option>
                                <?php while ($trainer = $trainers->fetch_assoc()): ?>
                                    <option value="<?= $trainer['id'] ?>"><?= htmlspecialchars($trainer['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Class Image *</label>
                        <div class="file-upload">
                            <label class="file-upload-label" for="image">
                                <i class="fas fa-cloud-upload-alt"></i> Choose an image
                            </label>
                            <input type="file" id="image" name="image" class="file-upload-input" accept="image/*" required>
                            <div class="file-upload-name" id="file-name">No file chosen</div>
                            <img id="preview" class="preview-image" src="#" alt="Preview">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> Save Class
                        </button>
                    </div>
                </form>
            </div>

            <!-- Classes Table -->
            <table class="trainers-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Class Name</th>
                        <th>Schedule</th>
                        <th>Duration</th>
                        <th>Trainer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($classes->num_rows > 0): ?>
                        <?php while ($class = $classes->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/classes/<?= htmlspecialchars($class['image']) ?>" 
                                         alt="<?= htmlspecialchars($class['name']) ?>" 
                                         class="trainer-photo">
                                </td>
                                <td><?= htmlspecialchars($class['name']) ?></td>
                                <td><?= htmlspecialchars($class['schedule']) ?></td>
                                <td><?= htmlspecialchars($class['duration']) ?></td>
                                <td><?= $class['trainer_name'] ?? 'N/A' ?></td>
                                <td class="action-links">
                                    <a href="#" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="classes.php?delete=<?= $class['id'] ?>" 
                                       title="Delete" 
                                       onclick="return confirm('Are you sure you want to delete this class?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No classes found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        // Image preview script
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = document.getElementById('file-name');
            const preview = document.getElementById('preview');
            
            if (this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                fileName.textContent = 'No file chosen';
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>