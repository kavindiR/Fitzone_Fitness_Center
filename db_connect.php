<?php
<<<<<<< HEAD
// Local defaults (XAMPP), overridden by environment variables in Vercel.
$dbHost = getenv('DB_HOST') ?: "localhost";
$dbUser = getenv('DB_USER') ?: "root";
$dbPass = getenv('DB_PASSWORD') ?: "";
$dbName = getenv('DB_NAME') ?: "fitzone";
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

// Optional single-URL format support: mysql://user:pass@host:3306/dbname
$databaseUrl = getenv('DATABASE_URL');
if (!empty($databaseUrl)) {
    $parts = parse_url($databaseUrl);
    if ($parts !== false && isset($parts['host'], $parts['user'], $parts['path'])) {
        $dbHost = $parts['host'];
        $dbUser = $parts['user'];
        $dbPass = $parts['pass'] ?? "";
        $dbPort = isset($parts['port']) ? (int) $parts['port'] : 3306;
        $dbName = ltrim($parts['path'], '/');
    }
}

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
$conn->set_charset("utf8mb4");

=======
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password is empty
$dbname = "fitzone"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
>>>>>>> 7f24a02e809c8a6e726258b5b9ba23acb8a7028e
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
