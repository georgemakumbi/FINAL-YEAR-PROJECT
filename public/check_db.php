<?php
require_once '../bootstrap.php';
$res = $conn->query("SELECT candidate_id, first_name, image_path FROM candidates LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['candidate_id'] . " | Name: " . $row['first_name'] . " | Path: " . $row['image_path'] . "\n";
}
