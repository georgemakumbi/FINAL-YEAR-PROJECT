<?php
// Vercel PHP entrypoint - dynamically routes to public files
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

if ($path === '/' || $path === '') {
    $path = '/index.php';
}

$public_dir = realpath(__DIR__ . '/../public');
$target = realpath(__DIR__ . '/../public' . $path);

if ($target && strncmp($target, $public_dir, strlen($public_dir)) === 0 && is_file($target) && preg_match('/\.php$/', $target)) {
    // Set the working directory exactly to where the file is located
    chdir(dirname($target));
    require $target;
} else {
    http_response_code(404);
    echo '404 Not Found';
}
?>
