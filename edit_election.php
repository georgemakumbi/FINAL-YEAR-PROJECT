<?php
include 'db_connection.php';
require 'admin_security.php';
require_admin_login();
ensure_csrf_token();

$election_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$election_title = '';
$position = '';
$start_date = '';
$end_date = '';
$status = 'scheduled';
$error = '';

$allowed_statuses = ['scheduled', 'active', 'closed'];
$positions = [
    'Guild President',
    'Guild Vice President',
    'Secretary General',
    'Finance Minister',
    'Academic Affairs',
    'Sports Minister',
    'Cultural Affairs'
];

if ($election_id <= 0) {
    header("Location: admin_dashboard.php?error=Invalid election selected");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_election'])) {
    verify_csrf_or_die();

    $posted_id = (int)($_POST['election_id'] ?? 0);
    $election_title = trim($_POST['election_title'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? 'scheduled');

    if ($posted_id !== $election_id) {
        $error = "Election ID mismatch.";
    } elseif ($election_title === '' || $position === '' || $start_date === '' || $end_date === '') {
        $error = "All fields are required.";
    } elseif (!in_array($status, $allowed_statuses, true)) {
        $error = "Invalid election status.";
    } else {
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);

        if ($start_ts === false || $end_ts === false) {
            $error = "Invalid date format.";
        } elseif ($end_ts <= $start_ts) {
            $error = "End date must be after start date.";
        }
    }

    if ($error === '') {
        $db_start = date('Y-m-d H:i:s', strtotime($start_date));
        $db_end = date('Y-m-d H:i:s', strtotime($end_date));

        $stmt = $conn->prepare("UPDATE elections SET election_title = ?, position = ?, start_date = ?, end_date = ?, status = ? WHERE election_id = ?");
        $stmt->bind_param("sssssi", $election_title, $position, $db_start, $db_end, $status, $election_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: admin_dashboard.php?success=Election updated successfully");
            exit();
        }

        $error = "Failed to update election.";
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT election_title, position, start_date, end_date, status FROM elections WHERE election_id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: admin_dashboard.php?error=Election not found");
    exit();
}

$election = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error === '') {
    $election_title = $election['election_title'];
    $position = $election['position'];
    $start_date = $election['start_date'];
    $end_date = $election['end_date'];
    $status = $election['status'];
}

$start_date_input = date('Y-m-d\TH:i', strtotime($start_date));
$end_date_input = date('Y-m-d\TH:i', strtotime($end_date));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Election - Admin</title>
    <link rel="icon" href="images/image.png" type="png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f4f7fb;
            padding: 24px;
            color: #2c3e50;
        }

        .container {
            max-width: 680px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 28px;
        }

        h2 {
            margin-bottom: 18px;
            color: #003366;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d7dde5;
            border-radius: 6px;
            font-size: 14px;
        }

        .error {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 6px;
            background: #fdecea;
            border: 1px solid #f5c6cb;
            color: #a94442;
        }

        .actions {
            margin-top: 8px;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary {
            background: #003366;
            color: #fff;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Election</h2>

        <?php if ($error !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="election_id" value="<?php echo (int)$election_id; ?>">
            <input type="hidden" name="update_election" value="1">

            <div class="form-group">
                <label for="election_title">Election Title</label>
                <input id="election_title" type="text" name="election_title" required value="<?php echo htmlspecialchars($election_title); ?>">
            </div>

            <div class="form-group">
                <label for="position">Position</label>
                <select id="position" name="position" required>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?php echo htmlspecialchars($pos); ?>" <?php echo $position === $pos ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pos); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input id="start_date" type="datetime-local" name="start_date" required value="<?php echo htmlspecialchars($start_date_input); ?>">
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input id="end_date" type="datetime-local" name="end_date" required value="<?php echo htmlspecialchars($end_date_input); ?>">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="scheduled" <?php echo $status === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Update Election</button>
                <a class="btn btn-secondary" href="admin_dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
