<?php
require_once __DIR__ . '/../config/bootstrap.php';
$user = require_auth();
$pageTitle = 'Dashboard — Matendo Medics';

$pdo = DB::pdo();

// Pull role-specific summary
$role = $user['role'] ?? 'client';
$summary = ['requests' => 0, 'engagements' => 0, 'messages' => 0];
try {
    if ($role === 'professional') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM engagements WHERE professional_id = (SELECT id FROM professionals WHERE user_id = :u)");
        $stmt->execute([':u' => $user['id']]);
        $summary['engagements'] = (int)$stmt->fetchColumn();
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM facility_requests WHERE user_id = :u");
        $stmt->execute([':u' => $user['id']]);
        $summary['requests'] = (int)$stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM care_requests WHERE user_id = :u");
        $stmt->execute([':u' => $user['id']]);
        $summary['requests'] += (int)$stmt->fetchColumn();
    }
} catch (Throwable $e) { /* table may not exist yet */ }

include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Account · dashboard</span>
            <h1>Welcome, <?= e($user['name'] ?: $user['email']) ?></h1>
            <p class="lede">Role: <strong><?= e($role) ?></strong></p>
        </div>
        <div class="hero-art is-mark" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/matendo-mark.svg')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="stats-grid mt-32">
            <div class="stat"><i class="fas fa-paper-plane"></i><strong><?= (int)$summary['requests'] ?></strong><span>Hiring requests</span></div>
            <div class="stat"><i class="fas fa-handshake"></i><strong><?= (int)$summary['engagements'] ?></strong><span>Active engagements</span></div>
            <div class="stat"><i class="fas fa-comments"></i><strong><?= (int)$summary['messages'] ?></strong><span>Unread messages</span></div>
        </div>

        <div class="two-col mt-32">
            <div class="card">
                <h2>Quick actions</h2>
                <ul class="action-list">
                    <?php if ($role === 'professional'): ?>
                        <li><a href="<?= e(url('talent.php')) ?>"><i class="fas fa-id-badge"></i> Update profile</a></li>
                        <li><a href="#"><i class="fas fa-briefcase"></i> Browse open shifts</a></li>
                        <li><a href="#"><i class="fas fa-clock"></i> Submit timesheet</a></li>
                    <?php else: ?>
                        <li><a href="<?= e(url('hire.php')) ?>"><i class="fas fa-plus"></i> Submit a hiring request</a></li>
                        <li><a href="<?= e(url('talent.php')) ?>"><i class="fas fa-search"></i> Browse the network</a></li>
                        <li><a href="#"><i class="fas fa-file-invoice"></i> View invoices</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card">
                <h2>Account</h2>
                <ul class="action-list">
                    <li><a href="#"><i class="fas fa-user-cog"></i> Account settings</a></li>
                    <li><a href="#"><i class="fas fa-shield-alt"></i> Security &amp; MFA</a></li>
                    <li><a href="<?= e(url('auth/logout.php')) ?>"><i class="fas fa-sign-out-alt"></i> Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
