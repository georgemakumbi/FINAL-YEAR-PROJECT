<?php
require 'admin_security.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/image.png" type="png">
    <title>View Feedback</title>
    <style>
        <?php include 'styles/theme.css'; ?>
        *{
            font-family: Arial, sans-serif;
        }
        body {
            background-color: var(--app-bg);
            color: var(--text);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid var(--border);
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: var(--surface-2);
        }
        button {
            padding: 10px 15px;
            background-color: var(--brand-primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button a {
            color: whitesmoke;
            text-decoration: none;
        }
        footer {
            margin-top: 50px;
            padding: 40px 20px;
            background: var(--footer-bg);
            color: var(--footer-text);
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Feedback from Students</h1>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Feedback</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            <!-- Additional rows can be dynamically added here -->
            <?php
            // Database connection
            // Database connection loaded via bootstrap.php

            // Fetch feedback data
            $sql = "SELECT student_id, feedback, feedback_date FROM feedback";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['feedback']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['feedback_date']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No feedback found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
    <footer>
            <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
            <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        </footer>
    <button><a href="admin_dashboard.php">Back to Dashboard</a></button>
    <script src="includes/theme.js" defer></script>
</body>
</html>
