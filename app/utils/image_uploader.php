<?php
/**
 * Uploads a candidate image.
 * If Vercel Blob is configured (token is set), it uploads to Vercel Blob.
 * Otherwise, it falls back to local file upload.
 * 
 * @param array $file The $_FILES['candidate_image'] array.
 * @param string|null &$error Outputs the error message if upload fails.
 * @return string|null Returns the image path (relative local path or remote URL), or null on failure.
 */
function upload_candidate_image($file, &$error = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error = "No file uploaded or file upload error occurred.";
        return null;
    }

    $tmpPath = $file['tmp_name'];
    $size = (int)$file['size'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($size <= 0 || $size > $maxSize) {
        $error = "Image too large. Max 2MB.";
        return null;
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
        $error = "Invalid image format. Only JPG, PNG, and WEBP are allowed.";
        return null;
    }

    $ext = $allowed[$mime];
    $filename = 'candidate_' . bin2hex(random_bytes(8)) . '.' . $ext;

    // Check if Vercel Blob token is set
    $token = getenv('VERCEL_BLOB_READ_WRITE_TOKEN') ?: ($_ENV['VERCEL_BLOB_READ_WRITE_TOKEN'] ?? null);
    if (!$token) {
        $token = getenv('BLOB_READ_WRITE_TOKEN') ?: ($_ENV['BLOB_READ_WRITE_TOKEN'] ?? null);
    }

    if ($token) {
        // --- Upload to Vercel Blob ---
        $binary = file_get_contents($tmpPath);
        if ($binary === false) {
            $error = "Failed to read uploaded file.";
            return null;
        }

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
            $error = "Vercel Blob upload failed: " . $curlErr;
            return null;
        }

        $decoded = json_decode($response, true);
        $imageUrl = $decoded['url'] ?? $decoded['blobUrl'] ?? $decoded['image_url'] ?? null;

        if (!$imageUrl) {
            $error = "Unexpected Blob response. Code: " . $httpCode;
            return null;
        }

        return $imageUrl;
    } else {
        // --- Fallback: Upload Locally ---
        $upload_dir = PROJECT_ROOT . '/public/candidates/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $error = "Failed to create local upload directory.";
                return null;
            }
        }

        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($tmpPath, $target_file)) {
            // Store RELATIVE path in DB (relative to public)
            return 'candidates/' . $filename;
        } else {
            $error = "Failed to move uploaded file to destination.";
            return null;
        }
    }
}
