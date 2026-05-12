<?php
// Start PHP session if needed (for user authentication)
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FitZone Fitness Center</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <header class="main-header">
        <a href="#home" class="logo">Fitzone <span>Fitness</span></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="membership.php">Membership</a>
            <a href="classes.php">Classes</a>
            <a href="blog.php">Blogs</a>
            <a href="contact.php">Contact</a>
            <a href="shop.php">Shop</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn-login">Login</a>
            <?php else: ?>
            <a href="logout.php" class="btn-logout">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-slider">
            <div class="slide active">
                <img src="assets/img/gym-slide1.png" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="assets/img/gym-slide2.png" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="assets/img/gym-slide3.png" alt="Slide 3">
            </div>
        </div>

        <div class="hero-content">
            <h1>Welcome to <span class="fitzone">FitZone</span> Fitness Center</h1>
            <p>Your journey to a stronger you starts here!</p>
            <button onclick="window.location.href='register.php'">Join Now</button>
        </div>
    </section>

    <style>
        /*===========================================================================================================*/
        /* ============================================ Hero Section ============================================ */
        .hero-section {
            position: relative;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            color: #fff;
            text-align: center;
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

            .slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .slide.active {
                opacity: 1;
            }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
            animation: fadeInUp 1.5s ease-out;
        }

        .fitzone {
            color: #a40000;
        }


        .hero-content h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            font-family: 'Anton', sans-serif;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .hero-content button {
            background-color: #a40000;
            color: white;
            padding: 12px 30px;
            font-size: 1.2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'Roboto', sans-serif;
        }

            .hero-content button:hover {
                background-color: #ff3333;
            }

        /* Optional nice fade-up animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        let slides = document.querySelectorAll('.slide');
        let currentSlide = 0;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(nextSlide, 4000); // Change slide every 4 seconds
    </script>

    <!-- Our Services Section -->
    <section id="services">
        <h2 class="section-title">Our Services</h2>
        <div class="services-container">

            <!-- Service 1 -->
            <div class="service-item">
                <div class="service-image">
                    <img src="assets/img/personal_training.png" alt="Personal Training">
                </div>
                <div class="service-description">
                    <h3>Personal Training</h3>
                    <p>Our expert trainers will tailor a workout plan just for you, helping you achieve your fitness goals faster and more efficiently.</p>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="service-item">
                <div class="service-image">
                    <img src="assets/img/group_classes.png" alt="Group Classes">
                </div>
                <div class="service-description">
                    <h3>Group Classes</h3>
                    <p>Join our high-energy group classes to stay motivated and have fun while getting fit with others!</p>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="service-item">
                <div class="service-image">
                    <img src="assets/img/nutrition_counseling.png" alt="Nutrition Counseling">
                </div>
                <div class="service-description">
                    <h3>Nutrition Counseling</h3>
                    <p>Get personalized nutrition advice from certified experts to complement your fitness journey.</p>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="service-item">
                <div class="service-image">
                    <img src="assets/img/state_of_art_equipment.png" alt="State-of-Art Equipment">
                </div>
                <div class="service-description">
                    <h3>State-of-Art Equipment</h3>
                    <p>Experience the latest and most effective fitness equipment that will take your workouts to the next level.</p>
                </div>
            </div>
        </div>
    </section>
    <style>
        /*==============================================================================================================================================*/
        /* === Our Services Section === */
        #services {
            padding: 100px 20px;
            background-color: #000000;
            text-align: center;
        }

        /* Title for Our Services */
        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 4rem;
            color: #fff;
            margin-bottom: 60px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        /* Services Container */
        .services-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive grid layout */
            gap: 40px;
            padding: 20px;
        }

        /* Individual Service Item */
        .service-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #a40000;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            overflow: hidden;
        }

            .service-item:hover {
                transform: translateY(-10px); /* Lift effect on hover */
                box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
                background-color: #c1272d; /* Add a bold red hover effect */
            }

            .service-item .service-image {
                width: 100%;
                height: 220px;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 20px;
                transition: transform 0.3s ease;
            }

                .service-item .service-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s ease;
                }

            .service-item:hover .service-image img {
                transform: scale(1.1); /* Zoom effect for images */
            }

        /* Description Section */
        .service-description {
            text-align: center;
            padding: 20px;
            font-size: 1.1rem;
        }

            .service-description h3 {
                font-family: 'Anton', sans-serif;
                font-size: 1.8rem;
                margin-bottom: 15px;
                font-weight: 700;
            }

            .service-description p {
                color: #ddd;
            }

        /* Scroll triggered animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Applying the animation to each service item */
        .service-item {
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
        }

            /* Adding staggered animation delay for each item */
            .service-item:nth-child(1) {
                animation-delay: 0.3s;
            }

            .service-item:nth-child(2) {
                animation-delay: 0.6s;
            }

            .service-item:nth-child(3) {
                animation-delay: 0.9s;
            }

        /* Responsive Design for Smaller Screens */
        @media screen and (max-width: 768px) {
            .services-container {
                grid-template-columns: 1fr; /* Stack items in one column on small screens */
            }

            .service-item {
                padding: 20px;
            }
        }
    </style>


    <!-- Programs Section -->
    <section id="programs">
        <h2 class="section-title">Our Special Classes</h2>
        <div class="programs-grid">


            <!-- Programs Container with Scroll -->
            <div class="programs-grid">
                <div class="program-card">
                    <img src="assets/img/strength_training.png" alt="Strength Training">
                    <div class="program-info">
                        <h3>Strength Training</h3>
                        <p>Build muscle and improve endurance.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/cardio_fitness.png" alt="Cardio Sessions">
                    <div class="program-info">
                        <h3>Cardio Sessions</h3>
                        <p>Boost your stamina and burn calories fast.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/yoga.png" alt="Yoga & Flexibility">
                    <div class="program-info">
                        <h3>Yoga & Flexibility</h3>
                        <p>Improve posture and relax your body and mind.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/pilates.png" alt="Pilates">
                    <div class="program-info">
                        <h3>Pilates</h3>
                        <p>Core strength and overall body flexibility.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/zumba.png" alt="Zumba">
                    <div class="program-info">
                        <h3>Zumba</h3>
                        <p>Dance fitness for weight loss and fun.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/crossfit.png" alt="CrossFit">
                    <div class="program-info">
                        <h3>CrossFit</h3>
                        <p>High-intensity workout for strength and stamina.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/weight_loss.png" alt="Martial Arts">
                    <div class="program-info">
                        <h3>Weight Loss</h3>
                        <p>Targeted training for effective fat loss and toning.</p>
                    </div>
                </div>

                <div class="program-card">
                    <img src="assets/img/boxing.png" alt="Boxing">
                    <div class="program-info">
                        <h3>Boxing</h3>
                        <p>Cardio and strength training through boxing.</p>
                    </div>
                </div>
            </div>


    </section>
    <style>
        /* =========================================== Programs Section ========================================= */
        /* === Programs Section === */
        #programs {
            padding: 100px 20px;
            text-align: center;
            background-color: #000000;
        }

        /* Title of the Section */
        .section-title {
            font-family: 'Anton', sans-serif;
            font-size: 3.5rem;
            margin-bottom: 40px;
            color: #fff;
            letter-spacing: 2px;
            font-size: 70px;
            text-transform: none; /* Ensures the text is not in uppercase */
        }

        /* Programs Grid */
        .programs-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 20px;
            flex-wrap: wrap;
            text-align: center;
        }

        /* Program Card */
        .program-card {
            width: 300px; /* Fixed size for all program boxes */
            height: 400px; /* Consistent height for all program cards */
            background-color: #444;
            border-radius: 15px;
            border: 3px solid maroon; /* Red border */
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: #ffffff;
            position: relative;
            transition: transform 0.3s ease, border 0.3s ease; /* Adding transition for border */
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards; /* Animation for entry */
        }

        /* Fade In and Slide Up Animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover Effects */
        .program-card:hover {
            transform: scale(1.05);
            border: 3px solid #a51f25;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.6); /* Enhance shadow on hover */
        }

            /* Hover animation for image zoom */
            .program-card:hover img {
                transform: scale(1.1);
                transition: transform 0.3s ease;
            }

        /* Image styles */
        .program-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        /* Program Info */
        .program-info {
            padding: 20px;
            background: rgba(0, 0, 0, 0.6);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

            .program-info h3 {
                font-size: 1.8rem;
                margin-bottom: 10px;
            }

            .program-info p {
                font-size: 1rem;
                color: #ddd;
            }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background: #c1272d;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            transition: background 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }

            .btn:hover {
                background: #a51f25;
                transform: scale(1.05);
            }
    </style>

    <!-- BMI Calculator Section -->
    <section id="bmi-calculator" class="bmi-section">
        <h2 class="section-title">Lets Calculate Your BMI</h2>
        <div class="bmi-container">
            <input type="number" id="weight" placeholder="Weight (kg)" class="bmi-input" />
            <input type="number" id="height" placeholder="Height (cm)" class="bmi-input" />
            <button id="calculate-bmi" class="bmi-btn">Calculate BMI</button>
            <div id="bmi-result" class="bmi-result">Your BMI will appear here.</div>
        </div>
    </section>
    <style>
        /* ================================================================== BMI Calculator============================================================= */
        /* BMI Calculator Styles */
        .bmi-section {
            background-color: #000000;
            padding: 50px 20px;
            text-align: center;
            color: #a40000;
            position: relative; /* To position the lines */
        }

            .bmi-section::before,
            .bmi-section::after {
                content: "";
                position: absolute;
                width: 100%;
                height: 2px;
                background-color: #c1272d;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
            }

            .bmi-section::after {
                top: auto;
                bottom: 0;
            }

            /* Adjust the positioning of the lines */
            .bmi-section::before {
                top: 20px; /* Position the top line */
            }

            .bmi-section::after {
                bottom: 20px; /* Position the bottom line */
            }

        .bmi-container {
            background-color: #000000;
            backdrop-filter: blur(8px);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            margin: 0 auto;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Input Fields */
        .bmi-input {
            padding: 10px;
            margin: 15px 0;
            width: 80%;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* Button Styles */
        .bmi-btn {
            padding: 10px 30px;
            background-color: #c1272d;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

            .bmi-btn:hover {
                background-color: #a51f25;
            }

        /* Result Text */
        .bmi-result {
            margin-top: 20px;
            font-size: 1.5rem;
            color: #ddd;
        }

        /* Add animations */
        .bmi-section h2 {
            animation: fadeInUp 1.5s ease-out;
        }

        /* Optional: Button Hover Animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        document.getElementById('calculate-bmi').addEventListener('click', function () {
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value) / 100; // Convert cm to m

            if (isNaN(weight)) {
                alert('Please enter a valid weight');
                return;
            }

            if (isNaN(height)) {
                alert('Please enter a valid height');
                return;
            }

            const bmi = weight / (height * height);
            let message = '';

            if (bmi < 18.5) {
                message = `Your BMI is ${bmi.toFixed(1)} (Underweight)`;
            } else if (bmi >= 18.5 && bmi < 25) {
                message = `Your BMI is ${bmi.toFixed(1)} (Normal weight)`;
            } else if (bmi >= 25 && bmi < 30) {
                message = `Your BMI is ${bmi.toFixed(1)} (Overweight)`;
            } else {
                message = `Your BMI is ${bmi.toFixed(1)} (Obese)`;
            }

            document.getElementById('bmi-result').textContent = message;
        });
    </script>

    <!-- Membership Plans Section -->
    <section class="membership-plans">
        <h2 class="section-title">Choose Your Plan</h2> <!-- This is the heading text -->

        <div class="membership-plans-container">
            <div class="plan">
                <div class="plan-icon">
                    <img src="assets/img/basic-plan-icon.png" alt="Basic Plan Icon" />
                </div>
                <h2>Basic Plan</h2>
                <p class="price">Rs. 3,000 / month</p>
                <ul>
                    <li>Gym Access (8AM - 6PM)</li>
                    <li>2 Group Classes per Week</li>
                    <li>No Personal Trainer</li>
                </ul>
                <a href="basic_register.php" class="btn">Join Now</a>
            </div>

            <div class="plan popular">
                <div class="plan-icon">
                    <img src="assets/img/premium-plan-icon.png" alt="Premium Plan Icon" />
                </div>
                <h2>Premium Plan</h2>
                <p class="price">Rs. 5,000 / month</p>
                <ul>
                    <li>Full Day Gym Access</li>
                    <li>Unlimited Group Classes</li>
                    <li>1 Personal Training Session / Week</li>
                </ul>
                <a href="premium_register.php" class="btn">Join Now</a>
            </div>

            <div class="plan">
                <div class="plan-icon">
                    <img src="assets/img/elite-plan-icon.png" alt="Elite Plan Icon" />
                </div>
                <h2>Elite Plan</h2>
                <p class="price">Rs. 8,000 / month</p>
                <ul>
                    <li>Full Access + Weekend Use</li>
                    <li>Unlimited Group Classes</li>
                    <li>Personal Trainer + Nutrition Guide</li>
                </ul>
                <a href="elite_register.php" class="btn">Join Now</a>
            </div>
        </div>

        <!-- Redirect Button to Membership Page -->
        <div class="membership-button">
            <a href="membership.php" class="btn btn-primary">More About Membership Plans</a>
        </div>
    </section>
    <style>
        /* ==================================================================Membership Plans Overview======================================================== */
        /* Membership Plans Section */

        .membership-plans {
            background-color: #000;
            padding: 80px 20px;
            text-align: center;
        }

            /* Section Title */
            .membership-plans.section-title {
                font-size: 42px;
                font-weight: 800;
                margin-bottom: 100px;
                color: #fff;
                text-transform: none; /* Ensures the text is not in uppercase */
            }

        .section-title {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 100px;
            color: #fff;
            text-transform: none; /* Text stays as you type */
        }


        /* Container for Plans */
        .membership-plans-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        /* Plan Card */
        .plan {
            background-color: #1a1a1a;
            padding: 50px 20px;
            border-radius: 20px;
            width: 300px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

            /* Hover Effect */
            .plan:hover {
                transform: translateY(-10px) scale(1.03);
                box-shadow: 0 15px 30px rgba(255, 0, 0, 0.4);
            }

        /* Plan Icon */
        .plan-icon {
            margin-bottom: 20px;
        }

            .plan-icon img {
                width: 80px;
                height: 80px;
                object-fit: contain;
                filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.2));
                border-radius: 50%;
                border: 2px solid #000000;
            }

        /* Plan Title */
        .plan h2 {
            font-size: 28px;
            color: #fff;
            margin-bottom: 10px;
        }

        /* Plan Price */
        .price {
            font-size: 24px;
            color: maroon;
            margin-bottom: 20px;
            font-weight: 700;
        }

        /* Features List */
        .plan ul {
            list-style: none;
            padding: 0;
            margin-bottom: 25px;
        }

            .plan ul li {
                font-size: 16px;
                color: #ccc;
                margin-bottom: 10px;
                position: relative;
                padding-left: 20px;
            }

                .plan ul li::before {
                    color: maroon;
                    font-weight: bold;
                    position: absolute;
                    left: 0;
                }

        /* Join Button */
        .plan .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: maroon;
            color: #fff;
            font-weight: bold;
            border-radius: 30px;
            transition: background-color 0.3s, transform 0.3s;
        }

            .plan .btn:hover {
                background-color: #c40000;
                transform: scale(1.05);
            }

        /* Highlighted Popular Plan */
        .popular {
            background: linear-gradient(145deg, #1f1f1f, #2c2c2c);
            border: 2px solid maroon;
            transform: scale(1.05);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .membership-plans-container {
                flex-wrap: wrap;
            }

            .plan {
                width: 90%;
                margin: auto;
            }
        }


        .membership-button {
            margin-top: 50px;
            text-align: center;
        }

            .membership-button .btn-primary {
                background-color: #a40000;
                color: white;
                padding: 15px 30px;
                font-size: 1.2rem;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

                .membership-button .btn-primary:hover {
                    background-color: #a51f25;
                }

        @keyframes pulsePop {
            0%, 100% {
                transform: scale(1.05);
                box-shadow: 0 0 15px rgba(255, 0, 0, 0.2);
            }

            50% {
                transform: scale(1.08);
                box-shadow: 0 0 30px rgba(255, 0, 0, 0.5);
            }
        }

    </style>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-container">

            <!-- FAQ 1 -->
            <div class="faq-item">
                <h3 class="faq-question">What are the gym hours?</h3>
                <p class="faq-answer">Our gym is open every day from 6 AM to 10 PM, providing flexible hours for your fitness needs.</p>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item">
                <h3 class="faq-question">Do I need to book a class in advance?</h3>
                <p class="faq-answer">Yes, it's recommended to book group classes in advance through our website or mobile app to secure your spot.</p>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item">
                <h3 class="faq-question">Is personal training available?</h3>
                <p class="faq-answer">Yes, personal training is available under our premium and elite plans. You can also book one-off sessions if required.</p>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-item">
                <h3 class="faq-question">Can I cancel or modify my membership?</h3>
                <p class="faq-answer">You can modify or cancel your membership anytime by contacting our support team. We offer flexible plans to meet your needs.</p>
            </div>

            <!-- FAQ 5 -->
            <div class="faq-item">
                <h3 class="faq-question">Do you offer a trial period?</h3>
                <p class="faq-answer">Yes, we offer a 7-day free trial for all new members. Come and experience the facilities before committing to a full membership.</p>
            </div>

        </div>
    </section>
    <style>
        /* ==================================================== FAQS ============================================ */
        /* FAQ Section Styles */
        .faq-section {
            background-color: #000;
            padding: 80px 20px;
            text-align: center;
            position: relative;
        }

        .faq-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
            z-index: 1;
        }

        .faq-item {
            background-color: #1a1a1a;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

            .faq-item:hover {
                transform: translateY(-10px);
                box-shadow: 0 10px 20px rgba(255, 0, 0, 0.6);
            }

        .faq-question {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }

        .faq-answer {
            font-size: 1.2rem;
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* Add subtle color change on hover */
        .faq-item:hover .faq-question {
            color: #c1272d;
        }

        .faq-item:hover .faq-answer {
            color: #ddd;
        }

        /* Adding Bounce Animation on Page Load */
        @keyframes bounceIn {
            0% {
                transform: translateY(100px);
                opacity: 0;
            }

            60% {
                transform: translateY(-30px);
                opacity: 1;
            }

            100% {
                transform: translateY(0);
            }
        }

        /* Apply animation to each FAQ item */
        .faq-item {
            animation: bounceIn 0.6s ease-out;
        }

        /* Responsive Design for FAQ Section */
        @media (max-width: 768px) {
            .faq-container {
                gap: 20px;
            }

            .faq-question {
                font-size: 1.4rem;
            }

            .faq-answer {
                font-size: 1rem;
            }
        }
    </style>


    <!-- Reviews Section -->
    <section class="about-reviews">
        <h2>What Our Members Say</h2>
        <div class="review-container">
            <div class="review">
                <img src="assets/img/member1.png" alt="Member 1">
                <div class="testimonial-text">
                    <p>"FitZone completely changed my life. The trainers are super helpful and the environment is always motivating!"</p>
                    <strong>- Nuwan Jayasuriya</strong>
                </div>
            </div>
            <div class="review">
                <img src="assets/img/member2.png" alt="Member 2">
                <div class="testimonial-text">
                    <p>"The group classes are amazing, and I've lost over 10kg in just 3 months."</p>
                    <strong>- Dinithi Karunaratne</strong>
                </div>
            </div>
            <div class="review">
                <img src="assets/img/member3.png" alt="Member 3">
                <div class="testimonial-text">
                    <p>"Best gym experience I've had! The trainers are so friendly, and I've seen amazing results."</p>
                    <strong>- Pradeep Silva</strong>
                </div>
            </div>
            <div class="review">
                <img src="assets/img/member4.png" alt="Member 4">
                <div class="testimonial-text">
                    <p>"The atmosphere at FitZone is so positive! It's like a second family to me."</p>
                    <strong>- Shehani Perera</strong>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* =============================================================Reviews============================================================================= */
        /* Reviews Section */
        .about-reviews {
            padding: 50px 0;
            background-color: #000000;
        }

            .about-reviews h2 {
                font-family: 'Anton', sans-serif;
                color: #ddd;
                text-align: center;
                font-size: 70px;
                margin-bottom: 50px;
            }

        /* Container for the reviews */
        .review-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
            text-align: center; /* Center all text */
        }

        /* Individual review */
        .review {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 30px;
            background: #222; /* Adjusted background color */
            padding: 25px;
            border-radius: 20px;
            margin: 0 auto;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
        }

            .review:hover {
                background: #444;
                transform: translateY(-5px);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            }

            .review img {
                width: 90px;
                height: 90px;
                object-fit: cover;
                border-radius: 50%;
                border: 4px solid #a40000;
            }


        /* Text associated with the review */
        .testimonial-text {
            max-width: 700px;
            text-align: center;
        }

            .testimonial-text p {
                font-style: italic;
                color: #ffffff;
                font-size: 18px;
            }

            .testimonial-text strong {
                display: block;
                margin-top: 15px;
                font-size: 20px;
                color: #a40000;
                font-weight: bold;
            }
    </style>


<!-- Footer -->
<footer class="site-footer">
    <div class="footer-container">
        <!-- Footer Top Section -->
        <div class="footer-top">
            <div class="footer-column">
                <h3>About FitZone</h3>
                <p>Your premier fitness destination offering state-of-the-art facilities, expert trainers, and a variety of classes to help you achieve your fitness goals.</p>
                <div class="footer-logo">FitZone <span>Fitness</span></div>
            </div>

            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="membership.php">Membership</a></li>
                    <li><a href="classes.php">Classes</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Contact Info</h3>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, Colombo, Sri Lanka</li>
                    <li><i class="fas fa-phone"></i> +94 76 123 4567</li>
                    <li><i class="fas fa-envelope"></i> info@fitzone.com</li>
                    <li><i class="fas fa-clock"></i> Open Daily: 6:00 AM - 10:00 PM</li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/fitzone" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://instagram.com/fitzone" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://youtube.com/fitzone" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="https://wa.me/94761234567" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                
                <h3 class="newsletter-title">Newsletter</h3>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your Email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; 2025 FitZone Fitness Center. All Rights Reserved.
            </div>
            <div class="footer-links">
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms of Service</a>
                <a href="sitemap.php">Sitemap</a>
            </div>
        </div>
    </div>
    <style>
        .popular {
            background: linear-gradient(145deg, #1f1f1f, #2c2c2c);
            border: 2px solid maroon;
            transform: scale(1.05);
            animation: pulsePop 2s infinite ease-in-out;
        }
        .site-footer {
        background-color: #000;
        color: #fff;
        padding: 60px 0 0;
        font-family: 'Roboto', sans-serif;
        border-top: 2px solid #a40000;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-top {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 50px;
    }

    .footer-column h3 {
        font-size: 1.5rem;
        color: #a40000;
        margin-bottom: 25px;
        font-family: 'Anton', sans-serif;
        letter-spacing: 1px;
        position: relative;
    }

    .footer-column h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -10px;
        width: 50px;
        height: 3px;
        background-color: #a40000;
    }

    .footer-logo {
        font-family: 'Anton', sans-serif;
        font-size: 2rem;
        color: #fff;
        margin-top: 20px;
    }

    .footer-logo span {
        color: #a40000;
    }

    .footer-column p {
        color: #ccc;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
    }

    .footer-column ul li {
        margin-bottom: 12px;
    }

    .footer-column ul li a {
        color: #ccc;
        text-decoration: none;
        transition: color 0.3s, padding-left 0.3s;
    }

    .footer-column ul li a:hover {
        color: #a40000;
        padding-left: 5px;
    }

    .contact-info li {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: #ccc;
    }

    .contact-info i {
        color: #a40000;
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #222;
        color: #fff;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .social-links a:hover {
        background-color: #a40000;
        transform: translateY(-3px);
    }

    .newsletter-title {
        margin-top: 30px;
    }

    .newsletter-form {
        display: flex;
        margin-top: 15px;
    }

    .newsletter-form input {
        flex: 1;
        padding: 12px 15px;
        border: none;
        border-radius: 4px 0 0 4px;
        font-size: 14px;
    }

    .newsletter-form button {
        background-color: #a40000;
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .newsletter-form button:hover {
        background-color: #c1272d;
    }

    .footer-bottom {
        border-top: 1px solid #333;
        padding: 25px 0;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
    }

    .copyright {
        color: #999;
        font-size: 14px;
    }

    .footer-links {
        display: flex;
        gap: 20px;
    }

    .footer-links a {
        color: #999;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: #a40000;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-top {
            grid-template-columns: 1fr;
        }
        
        .footer-bottom {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .footer-links {
            justify-content: center;
        }
    }
    </style>
</footer>
   
</body>
</html>