<?php
/**
 * Vercel serverless entrypoint (vercel-php). Routes requests to the correct
 * project PHP file under the repository root.
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$bootstrap = 'api/index.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
$path = $path === null || $path === '' ? '/' : rawurldecode($path);
$path = '/' . ltrim($path, '/');
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/') ?: '/';
}

$candidate = $path === '/' ? '' : ltrim($path, '/');

if ($candidate !== '' && strpos($candidate, '..') !== false) {
    http_response_code(400);
    echo 'Bad Request';
    return;
}

if ($candidate !== '' && preg_match('#^(includes|node_modules|\.git|\.vercel)(/|$)#i', $candidate)) {
    http_response_code(404);
    echo 'Not Found';
    return;
}

$rel = null;
if ($candidate === '' || $candidate === '/') {
    $rel = 'index.php';
} elseif (substr(strtolower($candidate), -4) === '.php') {
    $full = $root . '/' . $candidate;
    $rel = is_file($full) ? $candidate : null;
} else {
    $php = $candidate . '.php';
    $full = $root . '/' . $php;
    $rel = is_file($full) ? $php : null;
}

if ($rel === null) {
    http_response_code(404);
    echo 'Not Found';
    return;
}

if ($rel === $bootstrap) {
    $rel = 'index.php';
}

$fullPath = $root . '/' . $rel;
if (!is_file($fullPath)) {
    http_response_code(404);
    echo 'Not Found';
    return;
}

require $fullPath;
