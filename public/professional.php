<?php
require_once __DIR__ . '/../config/bootstrap.php';
$ref = trim((string)($_GET['ref'] ?? ''));
$activeNav = 'talent';

$prof = null;
if (preg_match('/^[A-Z0-9\-]+$/', $ref)) {
    $stmt = DB::pdo()->prepare("
        SELECT * FROM professionals WHERE reference_number = :r AND status IN ('active','approved') LIMIT 1
    ");
    $stmt->execute([':r' => $ref]);
    $prof = $stmt->fetch();
}

if (!$prof) {
    http_response_code(404);
    $pageTitle = 'Profile not found';
    include __DIR__ . '/../includes/header.php';
    echo '<section class="section"><div class="container"><h1>Profile not found</h1><p>This profile does not exist or is not currently public.</p><p><a class="btn btn-primary" href="' . e(url('talent.php')) . '">â† Back to talent</a></p></div></section>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = 'Dr. ' . $prof['first_name'] . ' ' . $prof['last_name'] . ' â€” Matendo Medics';
$pageDescription = ($prof['headline'] ?? '') ?: ($prof['profession'] . ' on the Matendo network.');
include __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <article class="profile">
            <header class="profile-header">
                <img src="<?= e(asset($prof['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" class="profile-avatar">
                <div>
                    <h1>Dr. <?= e($prof['first_name'].' '.$prof['last_name']) ?></h1>
                    <p class="lede"><?= e($prof['profession']) ?><?= $prof['specialization'] ? ' Â· ' . e($prof['specialization']) : '' ?></p>
                    <p class="muted"><i class="fas fa-map-marker-alt"></i> <?= e($prof['preferred_location'] ?? 'â€”') ?> Â· <?= (int)$prof['years_experience'] ?> yrs experience</p>
                    <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$prof['rating_avg'], 1) ?> <span class="muted small">(<?= (int)$prof['rating_count'] ?> reviews)</span></p>

                    <ul class="badges">
                        <?php if ($prof['verified_license']): ?><li><i class="fas fa-check-circle"></i> License verified</li><?php endif; ?>
                        <?php if ($prof['verified_id']):      ?><li><i class="fas fa-id-card"></i> ID verified</li><?php endif; ?>
                        <?php if ($prof['background_check']): ?><li><i class="fas fa-shield-alt"></i> Background checked</li><?php endif; ?>
                    </ul>

                    <div class="hero-actions">
                        <?php if (current_user()): ?>
                            <a href="<?= e(url('hire.php')) ?>" class="btn btn-primary">Request engagement</a>
                            <a href="#" class="btn btn-outline">Message</a>
                        <?php else: ?>
                            <a href="<?= e(url('auth/login.php')) ?>" class="btn btn-primary">Sign in to contact</a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <?php if (!empty($prof['bio'])): ?>
                <section><h2>About</h2><p><?= nl2br(e($prof['bio'])) ?></p></section>
            <?php endif; ?>

            <section>
                <h2>Details</h2>
                <dl class="details">
                    <dt>Languages</dt><dd><?= e($prof['languages'] ?? 'â€”') ?></dd>
                    <dt>Hourly rate</dt><dd><?= $prof['hourly_rate_kes'] ? 'KES ' . number_format((float)$prof['hourly_rate_kes'], 0) : 'On request' ?></dd>
                    <dt>Available from</dt><dd><?= e($prof['available_from'] ?? 'Immediately') ?></dd>
                </dl>
            </section>
        </article>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
