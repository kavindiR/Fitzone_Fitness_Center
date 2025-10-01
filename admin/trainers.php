<?php
// admin/trainers.php
session_start();
require_once '../db_connect.php';

// Check admin authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete Trainer
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    
    // First get the photo path to delete the file
    $result = $conn->query("SELECT photo FROM trainers WHERE id = $id");
    if ($result->num_rows > 0) {
        $trainer = $result->fetch_assoc();
        $photo_path = "../uploads/trainers/" . $trainer['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path); // Delete the file
        }
    }
    
    $conn->query("DELETE FROM trainers WHERE id = $id");
    $_SESSION['message'] = "Trainer deleted successfully!";
    header("Location: trainers.php");
    exit();
}

// Handle Add Trainer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trainer'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $bio = $conn->real_escape_string($_POST['bio']);
    
    // Handle file upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/trainers/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                $photo = $file_name;
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                header("Location: trainers.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "File is not an image.";
            header("Location: trainers.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please upload a profile photo.";
        header("Location: trainers.php");
        exit();
    }
    
    $conn->query("INSERT INTO trainers (name, specialization, bio, photo) 
                 VALUES ('$name', '$specialization', '$bio', '$photo')");
    $_SESSION['message'] = "Trainer added successfully!";
    header("Location: trainers.php");
    exit();
}

// Fetch all trainers
$trainers = $conn->query("SELECT * FROM trainers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainers - FitZone Admin</title>
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
                <a href="trainers.php" class="menu-item active">
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
            <div class="page-header">
                <h1 class="page-title">Manage Trainers</h1>
                <a href="#add-trainer-form" class="btn">
                    <i class="fas fa-user-plus"></i> Add New Trainer
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

            <!-- Trainer Form -->
            <div id="add-trainer-form" class="trainer-form">
                <h2 style="margin-bottom: 20px; color: var(--primary);">Add New Trainer</h2>
                
                <form method="POST" action="trainers.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialization">Specialization *</label>
                            <input type="text" id="specialization" name="specialization" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Profile Photo *</label>
                        <div class="file-upload">
                            <label class="file-upload-label" for="photo">
                                <i class="fas fa-cloud-upload-alt"></i> Choose a photo
                            </label>
                            <input type="file" id="photo" name="photo" class="file-upload-input" accept="image/*" required>
                            <div class="file-upload-name" id="file-name">No file chosen</div>
                            <img id="preview" class="preview-image" src="#" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio/Description *</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_trainer" class="btn">
                            <i class="fas fa-plus"></i> Add Trainer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Trainers Table -->
            <table class="trainers-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($trainers->num_rows > 0): ?>
                        <?php while ($trainer = $trainers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $trainer['id'] ?></td>
                                <td>
                                    <img src="../uploads/trainers/<?= htmlspecialchars($trainer['photo']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?>" class="trainer-photo">
                                </td>
                                <td><?= htmlspecialchars($trainer['name']) ?></td>
                                <td><?= htmlspecialchars($trainer['specialization']) ?></td>
                                <td class="action-links">
                                    <a href="#" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="trainers.php?delete=<?= $trainer['id'] ?>" 
                                       title="Delete" onclick="return confirm('Are you sure you want to delete this trainer? This action cannot be undone!')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <a href="#" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No trainers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
    // JavaScript for file upload preview
    document.getElementById('photo').addEventListener('change', function(e) {
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