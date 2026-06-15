<?php
/**
 * =============================================================================
 * SETTINGS MODULE - VIEW
 * =============================================================================
 * Renders settings actions for super admins.
 * 
 * Requires (set by settings.logic.php):
 * - $settings_message (string)
 * - $settings_message_type (string)
 * - $is_super_admin (bool)
 * 
 * =============================================================================
 */
require_once __DIR__ . '/../common.php';
?>

<div class="card">
    <h2>⚙️ Settings</h2>

    <?php if ($settings_message): ?>
        <?php echo render_alert($settings_message_type, $settings_message); ?>
    <?php endif; ?>

    <?php if (!$is_super_admin): ?>
        <p style="color: var(--text-muted);">Only super admins can access these settings.</p>
    <?php else: ?>
        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
            <h3>📈 Results Publishing</h3>
            <p style="color: #7f8c8d; margin-bottom: 12px;">
                Status: <strong><?php echo $results_published ? 'Published' : 'Unpublished'; ?></strong>
            </p>
            <form method="post" style="display: inline;">
                <?php echo render_csrf_field(); ?>
                <input type="hidden" name="results_publish_action" value="<?php echo $results_published ? 'unpublish' : 'publish'; ?>">
                <button type="submit" class="btn <?php echo $results_published ? 'btn-secondary' : 'btn-primary'; ?>">
                    <?php echo $results_published ? 'Unpublish Results' : 'Publish Results'; ?>
                </button>
            </form>
        </div>

        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
            <h3>🏫 University Logo</h3>
            <p style="color: #7f8c8d; margin-bottom: 12px;">
                Change the university logo displayed across headers, favicons, and pages.
            </p>
            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 15px; flex-wrap: wrap;">
                <div>
                    <p style="font-weight: bold; margin-bottom: 5px;">Current Logo:</p>
                    <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="University Logo" style="max-height: 80px; max-width: 150px; object-fit: contain; border: 1px solid var(--border); padding: 5px; border-radius: 6px; background: white;">
                </div>
                <form method="post" enctype="multipart/form-data" style="max-width: 400px; flex-grow: 1;">
                    <?php echo render_csrf_field(); ?>
                    <input type="hidden" name="upload_logo_action" value="1">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: var(--text);">Select New Logo (JPG, PNG, WEBP, max 2MB):</label>
                        <input type="file" name="system_logo" accept="image/*" required style="width: 100%; padding: 10px; border: 1px solid var(--input-border); border-radius: 6px; background: var(--input-bg); color: var(--text);">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Logo</button>
                </form>
            </div>
        </div>

        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
            <h3>⏰ Voting Deadline</h3>
            <p style="color: #7f8c8d; margin-bottom: 12px;">
                Set the absolute deadline for voting.
            </p>
            <form method="post" style="max-width: 400px;">
                <?php echo render_csrf_field(); ?>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: var(--text);">Set Deadline (YYYY-MM-DD HH:MM:SS):</label>
                    <input type="text" name="deadline" required placeholder="2025-09-01 19:00:00" 
                           value="<?php echo safe_output($current_deadline); ?>" style="width: 100%; padding: 10px; border: 1px solid var(--input-border); border-radius: 6px; background: var(--input-bg); color: var(--text);">
                </div>
                <button type="submit" class="btn btn-primary">Save Deadline</button>
            </form>
            <?php if ($current_deadline): ?>
                <p style="margin-top: 15px; font-size: 16px; font-weight: bold;">
                    Current: <?php echo safe_output($current_deadline); ?>
                </p>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 10px;">
            <h3>Reset Voting Status</h3>
            <p style="color: #7f8c8d; margin-bottom: 12px;">
                Use this after an election ends to allow students to vote again in a new election cycle.
            </p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <form method="post" id="reset-has-voted-form">
                    <?php echo render_csrf_field(); ?>
                    <input type="hidden" name="reset_voting_action" value="reset_has_voted">
                    <button type="button" class="btn btn-warning" onclick="openResetModal('reset-has-voted-form', 'Reset has_voted for all students?');">
                        Reset has_voted Only
                    </button>
                </form>
                <form method="post" id="full-reset-form">
                    <?php echo render_csrf_field(); ?>
                    <input type="hidden" name="reset_voting_action" value="full_reset">
                    <button type="button" class="btn btn-danger" onclick="openResetModal('full-reset-form', 'This will clear ALL votes, reset candidate totals, and reset has_voted. Continue?');">
                        Full Election Reset
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="reset-modal" class="reset-modal" aria-hidden="true">
    <div class="reset-modal-overlay" onclick="closeResetModal()"></div>
    <div class="reset-modal-card" role="dialog" aria-modal="true" aria-labelledby="reset-modal-title">
        <h3 id="reset-modal-title">Confirm Reset</h3>
        <p id="reset-modal-message"></p>
        <label for="reset-modal-input" style="display: block; margin-top: 12px; font-weight: 600;">
            Type command to continue
        </label>
        <input id="reset-modal-input" type="text" autocomplete="off" style="margin-top: 6px;">
        <div id="reset-modal-error" style="display: none; color: #b91c1c; margin-top: 8px; font-size: 13px;">
            Incorrect command.
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px;">
            <button type="button" class="btn btn-secondary" onclick="closeResetModal()">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="confirmReset()">Confirm</button>
        </div>
    </div>
</div>

<style>
    .reset-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .reset-modal.show {
        display: flex;
    }

    .reset-modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .reset-modal-card {
        position: relative;
        background: var(--surface);
        color: var(--text);
        border-radius: 10px;
        padding: 20px;
        width: min(440px, 92vw);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        z-index: 1;
    }

    .reset-modal-card input {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--input-border);
        border-radius: 6px;
        background: var(--input-bg);
        color: var(--text);
    }
</style>

<script>
    let pendingResetFormId = null;

    function openResetModal(formId, message) {
        pendingResetFormId = formId;
        const modal = document.getElementById('reset-modal');
        const messageEl = document.getElementById('reset-modal-message');
        const inputEl = document.getElementById('reset-modal-input');
        const errorEl = document.getElementById('reset-modal-error');

        messageEl.textContent = message;
        inputEl.value = '';
        errorEl.style.display = 'none';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        setTimeout(() => inputEl.focus(), 0);
    }

    function closeResetModal() {
        const modal = document.getElementById('reset-modal');
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        pendingResetFormId = null;
    }

    function confirmReset() {
        const inputEl = document.getElementById('reset-modal-input');
        const errorEl = document.getElementById('reset-modal-error');

        if (inputEl.value !== 'RESET') {
            errorEl.style.display = 'block';
            inputEl.focus();
            return;
        }

        if (pendingResetFormId) {
            document.getElementById(pendingResetFormId).submit();
        }
    }
</script>

