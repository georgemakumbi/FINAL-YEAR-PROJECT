<?php
require 'db_connection.php';

$query = "ALTER TABLE candidates ADD COLUMN status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending'";

if ($conn->query($query) === TRUE) {
    echo "Successfully added status column to candidates table.\n";
} else {
    echo "Error updating table: " . $conn->error . "\n";
}
?>
