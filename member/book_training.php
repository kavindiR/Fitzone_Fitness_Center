<?php
// book_training.php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['user_id'];
    $trainer_id = $_POST['trainer_id'];
    $session_type = $_POST['session_type'];
    $session_date = $_POST['session_date'];
    $session_time = $_POST['session_time'];
    $duration = $_POST['duration'];
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Check if the trainer is available at the requested time
        $check_stmt = $conn->prepare("
            SELECT id FROM training_sessions 
            WHERE trainer_id = ? 
            AND session_date = ? 
            AND session_time = ? 
            AND status = 'scheduled'
        ");
        $check_stmt->bind_param('iss', $trainer_id, $session_date, $session_time);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "The trainer is not available at the selected time. Please choose another time.";
            header("Location: book_training.php");
            exit();
        } else {
            // Get trainer name for confirmation message
            $name_stmt = $conn->prepare("SELECT name FROM trainers WHERE id = ?");
            $name_stmt->bind_param('i', $trainer_id);
            $name_stmt->execute();
            $name_result = $name_stmt->get_result();
            $trainer = $name_result->fetch_assoc();
            $trainer_name = $trainer['name'];

            // Insert into database
            $stmt = $conn->prepare("
                INSERT INTO training_sessions 
                (member_id, trainer_id, session_type, session_date, session_time, duration, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('iisssis', $member_id, $trainer_id, $session_type, $session_date, $session_time, $duration, $notes);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = [
                    'title' => "Booking Confirmed!",
                    'message' => "Your session with $trainer_name on " . 
                                date('F j, Y', strtotime($session_date)) . 
                                " at " . date('g:i A', strtotime($session_time)) . 
                                " has been successfully booked.",
                    'details' => "Session Type: $session_type | Duration: $duration minutes"
                ];
                header("Location: book_training.php");
                exit();
            } else {
                throw new Exception("Failed to book session. Please try again.");
            }
        }
    } catch(Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: book_training.php");
        exit();
    }
}

// Check for success/error messages from session
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Fetch available trainers from database
$trainers = [];
// CORRECTED QUERY: Removed invalid 'status' condition and fixed column name
$trainer_stmt = $conn->prepare("SELECT id, name AS fullname, specialization FROM trainers");
$trainer_stmt->execute();
$trainer_result = $trainer_stmt->get_result();

while ($row = $trainer_result->fetch_assoc()) {
    $trainers[] = $row;
}

// If no trainers found in database (fallback with your specific list)
if (empty($trainers)) {
    $trainers = [
        ['id' => 1, 'fullname' => 'Amila Perera', 'specialization' => 'Strength & Conditioning'],
        ['id' => 2, 'fullname' => 'Sachini Fernando', 'specialization' => 'Yoga & Cardio Instructor'],
        ['id' => 3, 'fullname' => 'Kasun Rajapaksha', 'specialization' => 'Nutrition Expert'],
        ['id' => 4, 'fullname' => 'Dinesh Wickramasinghe', 'specialization' => 'Bootcamp & HIIT'],
        ['id' => 5, 'fullname' => 'Aruni Jayawardena', 'specialization' => 'Personal Trainer & Pilates Instructor'],
        ['id' => 6, 'fullname' => 'Haritha Piyumal', 'specialization' => 'Group Fitness & Cardio Expert'],
        ['id' => 7, 'fullname' => 'Tharindu Wijesuriya', 'specialization' => 'Rehabilitation & Mobility Specialist'],
        ['id' => 8, 'fullname' => 'Kavinda Rajapaksha', 'specialization' => 'Dance Fitness Instructor']
    ];
}

// Fetch user's upcoming sessions from database
$upcoming_sessions = [];
if (isset($_SESSION['user_id'])) {
    $member_id = $_SESSION['user_id'];
    $session_stmt = $conn->prepare("
        SELECT ts.*, t.name as trainer_name 
        FROM training_sessions ts
        JOIN trainers t ON ts.trainer_id = t.id
        WHERE ts.member_id = ? 
        AND ts.session_date >= CURDATE()
        AND ts.status = 'scheduled'
        ORDER BY ts.session_date, ts.session_time
    ");
    $session_stmt->bind_param('i', $member_id);
    $session_stmt->execute();
    $session_result = $session_stmt->get_result();
    
    while ($row = $session_result->fetch_assoc()) {
        $upcoming_sessions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Training Session - FitZone</title>
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

        /* Booking Form */
        .booking-form {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-submit {
            background-color: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Success Message */
        .booking-success {
            background-color: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            animation: fadeIn 0.5s ease-out;
        }

        .booking-success-icon {
            font-size: 2rem;
            color: #28a745;
            flex-shrink: 0;
        }

        .booking-success-content h3 {
            color: #28a745;
            font-size: 1.3rem;
            margin-bottom: 5px;
            font-family: 'Anton', sans-serif;
            letter-spacing: 0.5px;
        }

        .booking-success-content p {
            color: var(--text);
            margin-bottom: 5px;
        }

        .booking-success-content .details {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Error Message */
        .alert-error {
            background-color: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error i {
            font-size: 1.2rem;
        }

        /* Upcoming Sessions */
        .sessions-section {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .session-list {
            margin-top: 20px;
        }

        .session-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .session-item:hover {
            background-color: rgba(164, 0, 0, 0.2);
        }

        .session-info {
            flex: 1;
        }

        .session-trainer {
            font-weight: 700;
            color: var(--text);
        }

        .session-details {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .session-actions a {
            color: var(--primary);
            text-decoration: none;
            margin-left: 15px;
            transition: all 0.3s;
        }

        .session-actions a:hover {
            color: var(--primary-hover);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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

            .session-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .session-actions {
                margin-top: 10px;
                align-self: flex-end;
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
                <h1 class="welcome-title">Book a Training Session</h1>
                <p class="welcome-subtitle">Schedule one-on-one time with our expert trainers</p>
            </section>

            <?php if (isset($success)): ?>
                <div class="booking-success">
                    <div class="booking-success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="booking-success-content">
                        <h3><?= htmlspecialchars($success['title']) ?></h3>
                        <p><?= htmlspecialchars($success['message']) ?></p>
                        <div class="details">
                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($success['details']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="booking-form">
                <form method="POST">
                    <div class="form-group">
                        <label for="trainer_id" class="form-label">Select Trainer</label>
                        <select name="trainer_id" id="trainer_id" class="form-control" required>
                            <option value="">-- Select a Trainer --</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['id'] ?>">
                                    <?= htmlspecialchars($trainer['fullname']) ?> - <?= htmlspecialchars($trainer['specialization']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="session_type" class="form-label">Session Type</label>
                        <select name="session_type" id="session_type" class="form-control" required>
                            <option value="">-- Select Session Type --</option>
                            <option value="Strength Training">Strength Training</option>
                            <option value="Cardio">Cardio</option>
                            <option value="Weight Loss">Weight Loss</option>
                            <option value="Flexibility">Flexibility</option>
                            <option value="Sports Specific">Sports Specific</option>
                            <option value="Rehabilitation">Rehabilitation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="session_date" class="form-label">Session Date</label>
                        <input type="date" name="session_date" id="session_date" class="form-control" 
                               min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="session_time" class="form-label">Session Time</label>
                        <input type="time" name="session_time" id="session_time" class="form-control" 
                               min="06:00" max="21:00" required>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <select name="duration" id="duration" class="form-control" required>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60" selected>60 minutes</option>
                            <option value="90">90 minutes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Book Session</button>
                </form>
            </div>

            <?php if (!empty($upcoming_sessions)): ?>
                <div class="sessions-section">
                    <h2 class="welcome-title">Your Upcoming Sessions</h2>
                    <div class="session-list">
                        <?php foreach ($upcoming_sessions as $session): ?>
                            <div class="session-item">
                                <div class="session-info">
                                    <div class="session-trainer">
                                        <?= htmlspecialchars($session['trainer_name']) ?> - <?= htmlspecialchars($session['session_type']) ?>
                                    </div>
                                    <div class="session-details">
                                        <?= date('F j, Y', strtotime($session['session_date'])) ?> at 
                                        <?= date('g:i A', strtotime($session['session_time'])) ?> • 
                                        <?= $session['duration'] ?> minutes
                                    </div>
                                </div>
                                <div class="session-actions">
                                    <a href="cancel_session.php?id=<?= $session['id'] ?>" 
                                       onclick="return confirm('Are you sure you want to cancel this session?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Set minimum time based on selected date
        document.getElementById('session_date').addEventListener('change', function() {
            const today = new Date().toISOString().split('T')[0];
            const selectedDate = this.value;
            const timeInput = document.getElementById('session_time');
            
            if (selectedDate === today) {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const minHour = currentHour + 1;
                const minTime = `${minHour.toString().padStart(2, '0')}:${currentMinute.toString().padStart(2, '0')}`;
                timeInput.min = minTime;
            } else {
                timeInput.min = '06:00';
            }
        });
    </script>
</body>
</html>