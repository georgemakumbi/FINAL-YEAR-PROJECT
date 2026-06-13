<?php
/**
 * =============================================================================
 * VOTING PAGE — With Confirmation Modal & Improved UX (Phase 4)
 * =============================================================================
 *
 * WHAT'S NEW:
 *   1. Vote confirmation modal — shows summary before submitting
 *   2. Better candidate cards with improved layout
 *   3. Progress indicator showing how many positions selected
 *   4. Animated selection feedback
 *
 * =============================================================================
 */
require_once '../bootstrap.php';

ensure_csrf_token();

// ─── Require Login ───────────────────────────────────────────────────────────
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

// ─── Check Voting Status ─────────────────────────────────────────────────────
$student_id = $_SESSION['student_id'];
if (Student::hasVoted($conn, $student_id)) {
    $_SESSION['has_voted'] = 1;
    header("Location: results.php");
    exit();
}
$_SESSION['has_voted'] = 0;

// ─── Check Election Status ──────────────────────────────────────────────────
if (!Election::isVotingOpen($conn)) {
    header("Location: index.php?error=Voting is currently closed");
    exit();
}

// ─── Get Candidates ──────────────────────────────────────────────────────────
$voter_department = $_SESSION['department'] ?? '';
$candidates = Candidate::getVerifiedForVoter($conn, $voter_department);

// Group candidates by position for the template
$positions = [];
foreach ($candidates as $candidate) {
    $pos = $candidate['position'];
    if (!isset($positions[$pos])) {
        $positions[$pos] = [];
    }
    $positions[$pos][] = $candidate;
}
$total_positions = count($positions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote — Kyambogo University</title>
    <meta name="description" content="Cast your vote in the Kyambogo University student elections">
    <link rel="icon" href="../assets/images/image.png" type="image/png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        <?php include ASSETS_CSS . '/voting.css'; ?>

        /* ── Confirmation Modal Styles ───────────────────────────────── */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }
        .modal-overlay.active { display: flex; }

        .modal {
            background: var(--surface, #fff);
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }

        .modal h2 {
            text-align: center;
            color: var(--brand-primary, #1a5632);
            margin-bottom: 20px;
            font-size: 1.4rem;
        }

        .modal-vote-summary {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }
        .modal-vote-summary li {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid var(--border, #eee);
            font-size: 0.95rem;
        }
        .modal-vote-summary li:last-child { border-bottom: none; }
        .modal-position { color: var(--text-muted, #888); font-weight: 500; }
        .modal-candidate { font-weight: 700; color: var(--text, #333); }

        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .modal-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.1s, opacity 0.2s;
        }
        .modal-btn:hover { transform: scale(1.02); }
        .modal-btn:active { transform: scale(0.98); }
        .modal-btn-cancel {
            background: var(--surface-alt, #66cc13);
            color: var(--text, #0c0c0c);
        }
        .modal-btn-confirm {
            background: var(--brand-primary, #1a5632);
            color: #fff;
        }
        .modal-btn-confirm:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ── Progress Bar ─────────────────────────────────────────── */
        .progress-bar-container {
            background: var(--surface, #f0f0f0);
            border-radius: 20px;
            padding: 15px 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: var(--shadow, 0 2px 4px rgba(0,0,0,0.1));
        }
        .progress-text {
            font-size: 0.9rem;
            color: var(--text-muted, #666);
            margin-bottom: 8px;
        }
        .progress-track {
            background: var(--border, #ddd);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, var(--brand-primary, #1a5632), #28a745);
            height: 100%;
            border-radius: 10px;
            transition: width 0.4s ease;
            width: 0%;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/images/image.png" alt="Kyambogo University Logo">
            <div class="university-name">KYAMBOGO UNIVERSITY ONLINE VOTING SYSTEM</div>
        </div>
        <div class="user-info" style="display: flex; gap: 15px; align-items: center;">
            <a href="apply_candidate.php" class="vote-btn" style="padding: 5px 15px; text-decoration: none; font-size: 0.9em; width: auto; margin:0;">Apply for Candidacy</a>
            Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
            <form action="logout.php" method="POST" style="margin:0;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <h1>Cast Your Vote</h1>
        
        <!-- Progress Bar: Shows how many positions you've selected -->
        <div class="progress-bar-container">
            <div class="progress-text">
                <span id="selectedCount">0</span> of <?php echo $total_positions; ?> positions selected
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        <form id="votingForm" action="processvote.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <?php foreach ($positions as $position_name => $position_candidates): ?>
                <div class="position-section">
                    <h2 class="position-title"><?php echo htmlspecialchars($position_name); ?></h2>
                    <div class="candidates-grid">
                        <?php foreach ($position_candidates as $candidate): ?>
                            <div class="candidate-card" 
                                 onclick="selectCandidate(this, <?php echo htmlspecialchars(json_encode($candidate['position']), ENT_QUOTES, 'UTF-8'); ?>)"
                                 data-candidate-name="<?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?>"
                                 data-position="<?php echo htmlspecialchars($candidate['position']); ?>">
                                <input type="radio" 
                                       name="<?php echo htmlspecialchars($candidate['position']); ?>" 
                                       value="<?php echo (int)$candidate['candidate_id']; ?>" 
                                       class="hidden" required>
                                <?php 
                                    $img_path = $candidate['image_path'] ?? '';
                                    $src = $img_path ? htmlspecialchars($img_path) : '../assets/images/placeholder.png';
                                ?>
                                <img src="<?php echo $src; ?>" 
                                     alt="<?php echo htmlspecialchars($candidate['first_name']); ?>" 
                                     class="candidate-image">
                                <div class="candidate-name">
                                    <?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?>
                                </div>
                                <div cla .ss="candidate-manifesto">
                                    <?php echo htmlspecialchars($candidate['manifesto'] ?? ''); ?>
                                </div>
                                <div class="candidate-position"><?php echo htmlspecialchars($candidate['position']); ?></div>
                                <div class="candidate-faculty"><?php echo htmlspecialchars($candidate['faculty']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <button type="button" class="vote-btn" id="submitVote" onclick="showConfirmation()" disabled>
                Review & Submit Votes
            </button>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- CONFIRMATION MODAL                                                 -->
    <!-- This appears when the user clicks "Review & Submit Votes"          -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal">
            <h2>✅ Confirm Your Votes</h2>
            
            <ul class="modal-vote-summary" id="voteSummary">
                <!-- Filled dynamically by JavaScript -->
            </ul>
            
            <div class="modal-warning">
                ⚠️ <strong>This action cannot be undone.</strong> Once submitted, your vote is final.
            </div>
            
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" onclick="hideConfirmation()">
                    ← Go Back
                </button>
                <button class="modal-btn modal-btn-confirm" id="confirmBtn" onclick="submitVotes()">
                    Confirm & Submit ✓
                </button>
            </div>
        </div>
    </div>

    <footer>
        <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
        <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
    </footer>
    
    <script src="../assets/js/theme.js" defer></script>
    <script>
    /**
     * ═════════════════════════════════════════════════════════════════════
     * VOTING PAGE JAVASCRIPT — Candidate Selection, Modal & Progress
     * ═════════════════════════════════════════════════════════════════════
     */
    const totalPositions = <?php echo $total_positions; ?>;

    /**
     * Handle candidate card selection.
     * 
     * When a card is clicked:
     * 1. Deselect all other candidates for the same position
     * 2. Select the clicked candidate (visual + radio button)
     * 3. Update the progress bar
     */
    function selectCandidate(card, position) {
        // Deselect all cards for this position
        document.querySelectorAll('.candidate-card').forEach(c => {
            if (c.querySelector('input').name === position) {
                c.classList.remove('selected');
                c.querySelector('input').checked = false;
            }
        });
        
        // Select the clicked card
        card.classList.add('selected');
        card.querySelector('input').checked = true;
        
        // Update progress bar
        updateProgress();
    }

    /**
     * Update the progress bar showing how many positions have selections.
     */
    function updateProgress() {
        const radioGroups = {};
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            if (!radioGroups[radio.name]) radioGroups[radio.name] = false;
            if (radio.checked) radioGroups[radio.name] = true;
        });
        
        let selected = 0;
        for (const group in radioGroups) {
            if (radioGroups[group]) selected++;
        }
        
        // Update progress text and bar
        document.getElementById('selectedCount').textContent = selected;
        const percentage = (selected / totalPositions) * 100;
        document.getElementById('progressFill').style.width = percentage + '%';
        
        // Enable/disable submit button
        document.getElementById('submitVote').disabled = (selected < totalPositions);
    }

    /**
     * Show the confirmation modal with vote summary.
     * 
     * This reads all selected candidates and builds a summary list
     * so the voter can review before final submission.
     */
    function showConfirmation() {
        const summary = document.getElementById('voteSummary');
        summary.innerHTML = '';
        
        // Get all selected candidates
        const selectedCards = document.querySelectorAll('.candidate-card.selected');
        selectedCards.forEach(card => {
            const position = card.dataset.position;
            const name = card.dataset.candidateName;
            
            const li = document.createElement('li');
            li.innerHTML = `
                <span class="modal-position">${position}</span>
                <span class="modal-candidate">${name}</span>
            `;
            summary.appendChild(li);
        });
        
        // Show the modal
        document.getElementById('confirmModal').classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    /**
     * Hide the confirmation modal (user clicked "Go Back").
     */
    function hideConfirmation() {
        document.getElementById('confirmModal').classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }

    /**
     * Submit the vote form after confirmation.
     * 
     * Disables the confirm button to prevent double-submission,
     * then submits the actual HTML form.
     */
    function submitVotes() {
        const btn = document.getElementById('confirmBtn');
        btn.disabled = true;
        btn.textContent = 'Submitting...';
        
        // Submit the actual form
        document.getElementById('votingForm').submit();
    }

    // Close modal when clicking outside
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) hideConfirmation();
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideConfirmation();
    });

    // Initial progress check
    updateProgress();
    </script>
</body>
</html>
