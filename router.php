<?php
/**
 * Local development — same URL routing as on Vercel (extensionless + .php).
 *
 * From this folder run:
 *   php -S localhost:8080 router.php
 *
 * Then open http://localhost:8080/  (do not open HTML/PHP files via file://)
 */

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
$path = $path === null || $path === '' ? '/' : rawurldecode($path);

// Never serve internal PHP include files directly.
if (strpos($path, '/includes/') === 0) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

// Let PHP built-in server serve existing static files normally.
$fullPath = __DIR__ . $path;
if ($path !== '/' && file_exists($fullPath) && !is_dir($fullPath)) {
    return false;
}

require __DIR__ . '/api/index.php';
