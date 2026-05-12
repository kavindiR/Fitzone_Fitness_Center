<?php
// Local defaults (XAMPP), overridden by environment variables on Vercel.
$dbHost = getenv('DB_HOST') ?: "localhost";
$dbUser = getenv('DB_USER') ?: "root";
$dbPass = getenv('DB_PASSWORD') ?: "";
$dbName = getenv('DB_NAME') ?: "fitzone";
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
