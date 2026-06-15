<?php
/**
 * Sync results_status.txt across common locations.
 * Usage: php scripts/sync_results_status.php
 */
$paths = [
    __DIR__ . '/../storage/results_status.txt',
    __DIR__ . '/../app/config/results_status.txt',
    __DIR__ . '/../views/components/results_status.txt',
];

// Resolve absolute paths and existing files
$existing = [];
foreach ($paths as $p) {
    $real = realpath($p) ?: $p;
    if (file_exists($real)) {
        $existing[$real] = filemtime($real);
    }
}

if (count($existing) === 0) {
    // No file exists — create storage default
    $default = 'unpublished';
    file_put_contents($paths[0], $default);
    $content = $default;
    echo "No existing status files found. Created storage/results_status.txt with '$default'.\n";
} else {
    // Pick the newest file as source of truth
    arsort($existing);
    $source = array_key_first($existing);
    $content = trim((string)file_get_contents($source));
    echo "Using $source as source of truth (value: $content)\n";
}

// Write content to all target paths (create directories if needed)
foreach ($paths as $p) {
    $dir = dirname($p);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $ok = @file_put_contents($p, $content);
    if ($ok === false) {
        echo "Failed to write to: $p\n";
    } else {
        echo "Wrote status to: $p\n";
    }
}

echo "Sync complete.\n";
