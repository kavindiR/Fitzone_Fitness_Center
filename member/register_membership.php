<?php
// register_membership.php
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

// Database connection - make sure this is using mysqli (not PDO)
require_once('../db_connect.php'); // Ensure this file creates a mysqli connection ($conn)

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['user_id'];
    $plan_name = $_POST['plan_name'];
    $price = $_POST['price'];
    $start_date = date('Y-m-d');
    
    // Calculate end date (1 month from now)
    $end_date = date('Y-m-d', strtotime('+1 month'));
    
    try {
        // Insert into database using mysqli prepared statements
        $stmt = $conn->prepare("INSERT INTO memberships (member_id, plan_name, price, start_date, end_date) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $member_id, $plan_name, $price, $start_date, $end_date);
        $stmt->execute();
        
        // Update session with new membership info
        $_SESSION['membership_plan'] = $plan_name;
        $_SESSION['membership_since'] = date('F Y');
        
        // Redirect to dashboard with success message
        $_SESSION['success'] = "Successfully registered for $plan_name membership!";
        header("Location: member_dashboard.php");
        exit();
    } catch(mysqli_sql_exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Membership plans data (same as in index.php)
$membershipPlans = [
    [
        'name' => 'Basic Plan',
        'price' => '3000',
        'features' => ['Gym Access (8AM - 6PM)', '2 Group Classes per Week', 'No Personal Trainer'],
        'icon' => 'fa-id-card'
    ],
    [
        'name' => 'Premium Plan',
        'price' => '5000',
        'features' => ['Full Day Gym Access', 'Unlimited Group Classes', '1 Personal Training Session / Week'],
        'icon' => 'fa-crown'
    ],
    [
        'name' => 'Elite Plan',
        'price' => '8000',
        'features' => ['Full Access + Weekend Use', 'Unlimited Group Classes', 'Personal Trainer + Nutrition Guide'],
        'icon' => 'fa-trophy'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Membership - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Use the same styles as member_dashboard.php for consistency */
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

        /* Header - Matching member_dashboard.php */
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

        /* Membership Plans Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .plan-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            border-top: 5px solid var(--primary);
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(164, 0, 0, 0.3);
        }

        .plan-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .plan-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 15px;
            width: 60px;
            height: 60px;
            background-color: rgba(164, 0, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plan-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.8rem;
            color: white;
            letter-spacing: 1px;
        }

        .plan-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 20px 0;
            font-family: 'Anton', sans-serif;
        }

        .plan-features {
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-item i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .btn-select {
            background-color: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-select:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Current Membership Banner */
        .current-membership {
            background-color: rgba(76, 201, 240, 0.1);
            border: 1px solid #4cc9f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .current-membership i {
            color: #4cc9f0;
            font-size: 1.5rem;
            margin-right: 15px;
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

            .plans-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .close {
            color: var(--text-secondary);
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: var(--primary);
        }

        .modal-title {
            font-family: 'Anton', sans-serif;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .modal-message {
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-confirm {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-confirm:hover {
            background-color: var(--primary-hover);
        }

        .btn-cancel {
            background-color: transparent;
            color: var(--text-secondary);
            padding: 10px 20px;
            border: 1px solid var(--text-secondary);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            color: white;
            border-color: white;
        }

        /* Error/Success Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 700;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Header Matching member_dashboard.php -->
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
                <h1 class="welcome-title">Register for a Membership Plan</h1>
                <p class="welcome-subtitle">Choose the plan that best fits your fitness goals</p>
            </section>

            <?php if (isset($_SESSION['membership_plan'])): ?>
                <div class="current-membership">
                    <i class="fas fa-info-circle"></i>
                    <p>You are currently registered for the <strong><?= $_SESSION['membership_plan'] ?></strong> plan.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="plans-grid">
                <?php foreach ($membershipPlans as $plan): ?>
                    <div class="plan-card">
                        <div class="plan-header">
                            <div class="plan-icon">
                                <i class="fas <?= $plan['icon'] ?>"></i>
                            </div>
                            <h2 class="plan-title"><?= $plan['name'] ?></h2>
                        </div>
                        <div class="plan-price">Rs. <?= number_format($plan['price']) ?> / month</div>
                        <div class="plan-features">
                            <?php foreach ($plan['features'] as $feature): ?>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span><?= $feature ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn-select" 
                                onclick="showConfirmation('<?= $plan['name'] ?>', '<?= $plan['price'] ?>')">
                            Select Plan
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 class="modal-title">Confirm Membership Registration</h3>
            <p class="modal-message" id="modalMessage"></p>
            <form id="membershipForm" method="POST">
                <input type="hidden" name="plan_name" id="planNameInput">
                <input type="hidden" name="price" id="planPriceInput">
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-confirm">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show confirmation modal
        function showConfirmation(planName, planPrice) {
            const modal = document.getElementById('confirmationModal');
            const modalMessage = document.getElementById('modalMessage');
            const planNameInput = document.getElementById('planNameInput');
            const planPriceInput = document.getElementById('planPriceInput');
            
            modalMessage.textContent = `Are you sure you want to register for the ${planName} plan at Rs. ${planPrice}/month?`;
            planNameInput.value = planName;
            planPriceInput.value = planPrice;
            
            modal.style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('confirmationModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>