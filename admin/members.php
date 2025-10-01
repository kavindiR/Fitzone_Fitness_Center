<?php
// admin/members.php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

require_once '../db_connect.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
        header("Location: members.php");
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related records first to maintain referential integrity
        $conn->query("DELETE FROM class_registrations WHERE member_id = $id");
        $conn->query("DELETE FROM training_sessions WHERE member_id = $id");
        $conn->query("DELETE FROM memberships WHERE member_id = $id");
        $conn->query("DELETE FROM member_profiles WHERE member_id = $id");
        $conn->query("DELETE FROM inquiries WHERE user_id = $id");
        
        // Finally delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['message'] = "Member deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting member: " . $e->getMessage();
    }
    
    header("Location: members.php");
    exit();
}

// Handle form submission for adding/editing members
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($fullname)) {
        $_SESSION['error'] = "Full name is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
    } else {
        try {
            if ($id > 0) {
                // Update existing member
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $fullname, $email, $role, $status, $id);
            } else {
                // Add new member - password will be set to default "password123" (hashed)
                $password = password_hash('password123', PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $fullname, $email, $password, $role, $status);
            }
            
            $stmt->execute();
            
            $member_id = $id > 0 ? $id : $stmt->insert_id;
            
            // Handle profile data
            $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : NULL;
            $address = !empty($_POST['address']) ? trim($_POST['address']) : NULL;
            $gender = !empty($_POST['gender']) ? $_POST['gender'] : NULL;
            $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : NULL;
            $height = !empty($_POST['height']) ? floatval($_POST['height']) : NULL;
            $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : NULL;
            $fitness_goals = !empty($_POST['fitness_goals']) ? trim($_POST['fitness_goals']) : NULL;
            $medical_conditions = !empty($_POST['medical_conditions']) ? trim($_POST['medical_conditions']) : NULL;
            
            // Check if profile exists
            $profile_check = $conn->prepare("SELECT id FROM member_profiles WHERE member_id = ?");
            $profile_check->bind_param("i", $member_id);
            $profile_check->execute();
            $profile_check->store_result();
            
            if ($profile_check->num_rows > 0) {
                // Update profile
                $profile_stmt = $conn->prepare("UPDATE member_profiles SET 
                    phone = ?, address = ?, gender = ?, birthdate = ?, height = ?, weight = ?, 
                    fitness_goals = ?, medical_conditions = ?, updated_at = NOW()
                    WHERE member_id = ?");
                $profile_stmt->bind_param("ssssddssi", $phone, $address, $gender, $birthdate, 
                    $height, $weight, $fitness_goals, $medical_conditions, $member_id);
            } else {
                // Insert profile
                $profile_stmt = $conn->prepare("INSERT INTO member_profiles 
                    (member_id, fullname, email, phone, address, gender, birthdate, height, weight, 
                    fitness_goals, medical_conditions, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $profile_stmt->bind_param("isssssddsss", $member_id, $fullname, $email, $phone, $address, 
                    $gender, $birthdate, $height, $weight, $fitness_goals, $medical_conditions);
            }
            
            $profile_stmt->execute();
            
            $_SESSION['message'] = $id > 0 ? "Member updated successfully!" : "Member added successfully!";
            
            header("Location: members.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $_SESSION['error'] = "Email already exists!";
            } else {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Get all members with their profile data
$members_query = "
    SELECT u.*, 
           mp.phone, mp.address, mp.gender, mp.birthdate, mp.height, mp.weight, 
           mp.fitness_goals, mp.medical_conditions
    FROM users u
    LEFT JOIN member_profiles mp ON u.id = mp.member_id
    WHERE u.role = 'member'
    ORDER BY u.created_at DESC";
$members_result = $conn->query($members_query);

// Get member details for editing
$edit_member = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("
        SELECT u.*, 
               mp.phone, mp.address, mp.gender, mp.birthdate, mp.height, mp.weight, 
               mp.fitness_goals, mp.medical_conditions
        FROM users u
        LEFT JOIN member_profiles mp ON u.id = mp.member_id
        WHERE u.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_member = $result->fetch_assoc();
    
    if (!$edit_member) {
        $_SESSION['error'] = "Member not found!";
        header("Location: members.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - FitZone Admin</title>
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
                <a href="members.php" class="menu-item active">
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
            <div class="page-header">
                <h1 class="page-title">Manage Members</h1>
                <a href="members.php?action=add" class="btn">
                    <i class="fas fa-user-plus"></i> Add New Member
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

            <!-- Member Form (shown when adding/editing) -->
            <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
                <div class="member-form">
                    <h2 style="margin-bottom: 20px; color: var(--primary);">
                        <?= $_GET['action'] == 'add' ? 'Add New Member' : 'Edit Member' ?>
                    </h2>
                    
                    <form method="POST" action="members.php">
                        <input type="hidden" name="id" value="<?= $edit_member['id'] ?? 0 ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Full Name *</label>
                                <input type="text" id="fullname" name="fullname" class="form-control" 
                                    value="<?= htmlspecialchars($edit_member['fullname'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                    value="<?= htmlspecialchars($edit_member['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="member" <?= isset($edit_member['role']) && $edit_member['role'] == 'member' ? 'selected' : '' ?>>Member</option>
                                    <option value="trainer" <?= isset($edit_member['role']) && $edit_member['role'] == 'trainer' ? 'selected' : '' ?>>Trainer</option>
                                    <option value="admin" <?= isset($edit_member['role']) && $edit_member['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="active" <?= isset($edit_member['status']) && $edit_member['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= isset($edit_member['status']) && $edit_member['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="suspended" <?= isset($edit_member['status']) && $edit_member['status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" 
                                    value="<?= htmlspecialchars($edit_member['phone'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male" <?= isset($edit_member['gender']) && $edit_member['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= isset($edit_member['gender']) && $edit_member['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="other" <?= isset($edit_member['gender']) && $edit_member['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" class="form-control" rows="2"><?= htmlspecialchars($edit_member['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="birthdate">Birthdate</label>
                                <input type="date" id="birthdate" name="birthdate" class="form-control" 
                                    value="<?= $edit_member['birthdate'] ?? '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="height">Height (cm)</label>
                                <input type="number" id="height" name="height" class="form-control" step="0.01" 
                                    value="<?= $edit_member['height'] ?? '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="weight">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" class="form-control" step="0.01" 
                                    value="<?= $edit_member['weight'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="fitness_goals">Fitness Goals</label>
                            <textarea id="fitness_goals" name="fitness_goals" class="form-control" rows="3"><?= htmlspecialchars($edit_member['fitness_goals'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="medical_conditions">Medical Conditions</label>
                            <textarea id="medical_conditions" name="medical_conditions" class="form-control" rows="3"><?= htmlspecialchars($edit_member['medical_conditions'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <a href="members.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn">Save Member</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Members Table -->
            <table class="members-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($members_result->num_rows > 0): ?>
                        <?php while ($member = $members_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $member['id'] ?></td>
                                <td><?= htmlspecialchars($member['fullname']) ?></td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                                <td><?= $member['phone'] ? htmlspecialchars($member['phone']) : 'N/A' ?></td>
                                <td>
                                    <span class="status-badge status-<?= $member['status'] ?>">
                                        <?= ucfirst($member['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($member['created_at'])) ?></td>
                                <td class="action-links">
                                    <a href="members.php?action=edit&id=<?= $member['id'] ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="members.php?action=delete&id=<?= $member['id'] ?>" 
                                       title="Delete" onclick="return confirm('Are you sure you want to delete this member? This action cannot be undone!')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <a href="member_details.php?id=<?= $member['id'] ?>" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No members found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>