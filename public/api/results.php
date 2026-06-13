<?php
/**
 * =============================================================================
 * RESULTS API — Returns Election Results as JSON
 * =============================================================================
 * 
 * WHAT IS AN API ENDPOINT?
 *   A regular PHP page returns HTML (a complete web page).
 *   An API endpoint returns JSON (just the data).
 *   
 *   Regular page:  <html><body><h1>Results</h1>...</body></html>
 *   API endpoint:  {"results": [{"name": "John", "votes": 150}]}
 *
 * WHY JSON INSTEAD OF HTML?
 *   JavaScript can easily work with JSON to update the page
 *   without a full reload. This creates a "live" experience.
 *
 * HOW IT'S USED:
 *   // JavaScript on results.php:
 *   fetch('/finalyearproject/public/api/results.php')
 *     .then(response => response.json())
 *     .then(data => updateChart(data));
 *
 * SECURITY:
 *   - Requires student login (session check)
 *   - Returns empty data if results aren't published yet
 *   - Uses Models (no raw SQL in this file!)
 *
 * =============================================================================
 */

require_once '../../bootstrap.php';
require_once VIEWS_COMPONENTS . '/includes/results_publish.php';

// ─── Set JSON Response Headers ───────────────────────────────────────────────
// Content-Type tells the browser this is JSON data, not an HTML page.
// Access-Control headers allow the page's JavaScript to read the response.
header('Content-Type: application/json; charset=utf-8');

// ─── Check Authentication ────────────────────────────────────────────────────
if (!isset($_SESSION['student_id'])) {
    http_response_code(401); // 401 = Unauthorized
    echo json_encode(['error' => 'Authentication required']);
    exit();
}

// ─── Build Response ──────────────────────────────────────────────────────────
$response = [
    'success'           => true,
    'results_published' => false,
    'results'           => [],
    'statistics'        => [],
    'timestamp'         => date('Y-m-d H:i:s'),
];

// Only return results if they've been published by admin
if (results_are_published()) {
    $response['results_published'] = true;
    
    // Get all results using our Candidate model
    $raw_results = Candidate::getResults($conn);
    
    // Group results by position for easier frontend rendering
    $grouped = [];
    foreach ($raw_results as $row) {
        $position = $row['position'];
        if (!isset($grouped[$position])) {
            $grouped[$position] = [
                'position'   => $position,
                'candidates' => [],
            ];
        }
        $grouped[$position]['candidates'][] = [
            'candidate_id' => (int)$row['candidate_id'],
            'name'         => $row['first_name'] . ' ' . $row['last_name'],
            'faculty'      => $row['faculty'],
            'votes'        => (int)$row['votes'],
            'percentage'   => round((float)$row['percentage'], 1),
        ];
    }
    $response['results'] = array_values($grouped);
    
    // Add overall statistics
    $response['statistics'] = [
        'total_students' => Student::countAll($conn),
        'total_voted'    => Student::countVoted($conn),
        'turnout'        => Student::getVoterTurnout($conn),
        'total_votes'    => Vote::countAll($conn),
    ];
}

// ─── Send JSON Response ──────────────────────────────────────────────────────
// json_encode() converts a PHP array into a JSON string.
// JSON_PRETTY_PRINT makes it readable (optional, slightly larger response).
echo json_encode($response, JSON_PRETTY_PRINT);
