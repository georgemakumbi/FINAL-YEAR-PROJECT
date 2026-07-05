<?php
require_once '../bootstrap.php';
// admin_security.php is already loaded by bootstrap.php
require_admin_login();

$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;
if ($election_id <= 0) {
    header("Location: admin_dashboard.php?error=Invalid election selected");
    exit();
}

$election_stmt = $conn->prepare("SELECT election_id, election_title, position, start_date, end_date, status FROM elections WHERE election_id = ?");
$election_stmt->bind_param("i", $election_id);
$election_stmt->execute();
$election = $election_stmt->get_result()->fetch_assoc();
$election_stmt->close();

if (!$election) {
    header("Location: admin_dashboard.php?error=Election not found");
    exit();
}

$position = $election['position'];
$start_date = $election['start_date'];
$end_date = $election['end_date'];

$eligible_row = $conn->query("SELECT COUNT(*) AS total_students FROM students")->fetch_assoc();
$eligible_voters = (int)($eligible_row['total_students'] ?? 0);

$votes_stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total_votes,
        COUNT(DISTINCT receipt_token) AS unique_voters
    FROM votes
    WHERE position = ?
");
$votes_stmt->bind_param("s", $position);
$votes_stmt->execute();
$votes_summary = $votes_stmt->get_result()->fetch_assoc();
$votes_stmt->close();

$total_votes = (int)($votes_summary['total_votes'] ?? 0);
$unique_voters = (int)($votes_summary['unique_voters'] ?? 0);
$turnout = $eligible_voters > 0 ? round(($unique_voters / $eligible_voters) * 100, 2) : 0;

$candidates_stmt = $conn->prepare("
    SELECT
        c.candidate_id,
        c.first_name,
        c.last_name,
        c.faculty,
        COUNT(v.vote_id) AS election_votes
    FROM candidates c
    LEFT JOIN votes v
      ON v.candidate_id = c.candidate_id
     AND v.position = ?
    WHERE c.position = ?
    GROUP BY c.candidate_id, c.first_name, c.last_name, c.faculty
    ORDER BY election_votes DESC, c.first_name ASC, c.last_name ASC
");
$candidates_stmt->bind_param("ss", $position, $position);
$candidates_stmt->execute();
$candidate_result = $candidates_stmt->get_result();

$candidate_rows = [];
$highest_votes = -1;
while ($row = $candidate_result->fetch_assoc()) {
    $votes_for_candidate = (int)$row['election_votes'];
    $row['election_votes'] = $votes_for_candidate;
    $row['percentage'] = $total_votes > 0 ? round(($votes_for_candidate / $total_votes) * 100, 2) : 0;
    $candidate_rows[] = $row;
    if ($votes_for_candidate > $highest_votes) {
        $highest_votes = $votes_for_candidate;
    }
}
$candidates_stmt->close();

$winners = [];
if ($highest_votes >= 0) {
    foreach ($candidate_rows as $candidate_row) {
        if ($candidate_row['election_votes'] === $highest_votes) {
            $winners[] = trim($candidate_row['first_name'] . ' ' . $candidate_row['last_name']);
        }
    }
}



if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $safe_title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $election['election_title']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="election_report_' . $safe_title . '_' . $election_id . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Election Report']);
    fputcsv($output, ['Election ID', $election['election_id']]);
    fputcsv($output, ['Election Title', $election['election_title']]);
    fputcsv($output, ['Position', $position]);
    fputcsv($output, ['Start Date', $start_date]);
    fputcsv($output, ['End Date', $end_date]);
    fputcsv($output, ['Status', $election['status']]);
    fputcsv($output, ['Eligible Voters', $eligible_voters]);
    fputcsv($output, ['Voters Participated', $unique_voters]);
    fputcsv($output, ['Turnout (%)', $turnout]);
    fputcsv($output, ['Total Votes Cast', $total_votes]);
    fputcsv($output, []);
    fputcsv($output, ['Candidate Results']);
    fputcsv($output, ['Candidate ID', 'Candidate Name', 'Faculty', 'Votes', 'Percentage']);

    foreach ($candidate_rows as $candidate_row) {
        fputcsv($output, [
            $candidate_row['candidate_id'],
            trim($candidate_row['first_name'] . ' ' . $candidate_row['last_name']),
            $candidate_row['faculty'],
            $candidate_row['election_votes'],
            $candidate_row['percentage']
        ]);
    }

    fclose($output);
    exit();
}

$status_class = 'status-scheduled';
if ($election['status'] === 'active') {
    $status_class = 'status-active';
} elseif ($election['status'] === 'closed') {
    $status_class = 'status-closed';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">" type="image/png">
    <title>Election Report - <?php echo htmlspecialchars($election['election_title']); ?></title>
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        <?php include ASSETS_CSS . '/election_report.css'; ?>
    </style>
</head>
<body>
    <div class="container">
        <div class="toolbar no-print">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="election_report.php?election_id=<?php echo (int)$election['election_id']; ?>&export=csv" class="btn btn-primary">Export CSV</a>
            <button type="button" class="btn btn-success" onclick="window.print()">Print Report</button>
        </div>

        <div class="report-card">
            <h1>Election Report</h1>
            <p class="subtitle">Generated on <?php echo htmlspecialchars(date('Y-m-d H:i:s')); ?></p>

            <div class="meta-grid">
                <div><strong>Election ID:</strong> <?php echo (int)$election['election_id']; ?></div>
                <div><strong>Election Title:</strong> <?php echo htmlspecialchars($election['election_title']); ?></div>
                <div><strong>Position:</strong> <?php echo htmlspecialchars($position); ?></div>
                <div><strong>Status:</strong> <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst($election['status'])); ?></span></div>
                <div><strong>Start Date:</strong> <?php echo htmlspecialchars($start_date); ?></div>
                <div><strong>End Date:</strong> <?php echo htmlspecialchars($end_date); ?></div>
            </div>

            <div class="stats-grid">
                <div class="stat">
                    <div class="label">Eligible Voters</div>
                    <div class="value"><?php echo number_format($eligible_voters); ?></div>
                </div>
                <div class="stat">
                    <div class="label">Voters Participated</div>
                    <div class="value"><?php echo number_format($unique_voters); ?></div>
                </div>
                <div class="stat">
                    <div class="label">Turnout</div>
                    <div class="value"><?php echo number_format($turnout, 2); ?>%</div>
                </div>
                <div class="stat">
                    <div class="label">Total Votes Cast</div>
                    <div class="value"><?php echo number_format($total_votes); ?></div>
                </div>
            </div>

            <div class="winner-box">
                <strong>Winner<?php echo count($winners) === 1 ? '' : 's'; ?>:</strong>
                <?php echo !empty($winners) ? htmlspecialchars(implode(', ', $winners)) : 'No candidates found'; ?>
                <?php if (count($winners) > 1): ?>
                    <span class="tie-note">(Tie)</span>
                <?php endif; ?>
            </div>

            <h2>Candidate Results</h2>
            <table>
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Faculty</th>
                        <th>Votes</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($candidate_rows)): ?>
                        <?php foreach ($candidate_rows as $candidate_row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(trim($candidate_row['first_name'] . ' ' . $candidate_row['last_name'])); ?></td>
                                <td><?php echo htmlspecialchars($candidate_row['faculty']); ?></td>
                                <td><?php echo number_format($candidate_row['election_votes']); ?></td>
                                <td><?php echo number_format($candidate_row['percentage'], 2); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty">No candidates for this position.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <footer>
            <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
            <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        </footer>
    </div>
    <script src="../assets/js/theme.js" defer></script>
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>
