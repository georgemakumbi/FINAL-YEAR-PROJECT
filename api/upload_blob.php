<?php
// Upload a candidate image to Vercel Blob and return its URL.
// Expects multipart/form-data with field name: candidate_image
// Returns JSON: { "image_url": "..." }

require_once dirname(__DIR__) . '/bootstrap.php';

// NOTE: On Vercel serverless, this route must not touch the local filesystem.
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Vercel Blob integration requires an env var token.
$token = getenv('VERCEL_BLOB_READ_WRITE_TOKEN') ?: ($_ENV['VERCEL_BLOB_READ_WRITE_TOKEN'] ?? null);
if (!$token) {
    // Fallback: if you stored token under another name, this avoids silent failures.
    $token = getenv('BLOB_READ_WRITE_TOKEN') ?: ($_ENV['BLOB_READ_WRITE_TOKEN'] ?? null);
}
if (!$token) {
    http_response_code(500);
    echo json_encode(['error' => 'Missing env var: VERCEL_BLOB_READ_WRITE_TOKEN (or BLOB_READ_WRITE_TOKEN)']);
    exit;
}

$storeId = getenv('VERCEL_BLOB_STORE_ID') ?: ($_ENV['VERCEL_BLOB_STORE_ID'] ?? null);
if (!$storeId) {
    $storeId = getenv('BLOB_STORE_ID') ?: ($_ENV['BLOB_STORE_ID'] ?? null);
}

if (!isset($_FILES['candidate_image']) || $_FILES['candidate_image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing candidate_image upload']);
    exit;
}

$tmpPath = $_FILES['candidate_image']['tmp_name'];
$size = (int)($_FILES['candidate_image']['size'] ?? 0);
$maxSize = 2 * 1024 * 1024; // 2MB
if ($size <= 0 || $size > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file size']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $tmpPath);
finfo_close($finfo);

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format']);
    exit;
}

$ext = $allowed[$mime];
$filename = 'candidate_' . bin2hex(random_bytes(8)) . '.' . $ext;

$binary = file_get_contents($tmpPath);
if ($binary === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to read uploaded file']);
    exit;
}

// Vercel Blob upload endpoint.
// For the typical setup, Blob upload uses: https://blob.vercel-storage.com/upload
// with Authorization: Bearer <token>.
$uploadUrl = 'https://blob.vercel-storage.com/upload';

$ch = curl_init($uploadUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/octet-stream',
        'x-filename: ' . $filename,
        'x-content-type: ' . $mime,
    ],
    CURLOPT_POSTFIELDS => $binary,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed', 'detail' => $curlErr]);
    exit;
}

$decoded = json_decode($response, true);

// The response shape varies by Blob configuration. Common fields are: url or blobUrl or id.
$imageUrl = $decoded['url'] ?? $decoded['blobUrl'] ?? $decoded['image_url'] ?? null;

if (!$imageUrl) {
    http_response_code($httpCode >= 400 ? 500 : $httpCode);
    echo json_encode(['error' => 'Unexpected Blob response', 'detail' => $decoded, 'raw' => $response]);
    exit;
}

echo json_encode(['image_url' => $imageUrl, 'mime' => $mime, 'filename' => $filename]);
exit;
?>

