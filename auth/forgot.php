<?php
require_once __DIR__ . '/../config/bootstrap.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::requireCsrf();
    Security::rateLimit('auth-forgot', 3);
    $email = trim((string)($_POST['email'] ?? ''));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = DB::pdo()->prepare("SELECT id FROM users WHERE email = :e AND status = 'active'");
        $stmt->execute([':e' => $email]);
        $u = $stmt->fetch();
        if ($u) {
            $token = bin2hex(random_bytes(32));
            DB::pdo()->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (:u, :t, DATE_ADD(NOW(), INTERVAL 30 MINUTE))
            ")->execute([':u' => $u['id'], ':t' => hash('sha256', $token)]);

            $resetUrl = (Env::get('APP_URL', '') ?: '')
                . url('auth/reset.php') . '?token=' . urlencode($token);
            send_mail(
                $email,
                'Reset your Matendo Health password',
                "We received a request to reset your password.\n\n"
                . "Click the link below within 30 minutes to set a new password:\n\n"
                . $resetUrl . "\n\n"
                . "If you did not request this, you can safely ignore this email.\n\n"
                . "— Matendo Health"
            );
        }
    }
    $sent = true; // Always pretend success to avoid account enumeration.
}

$pageTitle = 'Reset password — Matendo Medics';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Account · reset</span>
            <h1>Reset password</h1>
            <p class="lede">We will send a one-time link to your email.</p>
        </div>
        <div class="hero-art is-mark" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/matendo-mark.svg')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section auth-page">
    <div class="container narrow">
        <div class="card">
            <?php if ($sent): ?>
                <div class="flash flash-success">If an account exists for that email, a reset link has been sent. Check your inbox.</div>
            <?php else: ?>
                <p class="muted">Enter your email and we will send you a reset link.</p>
                <form method="POST" novalidate>
                    <?= csrf_field() ?>
                    <div class="field"><label>Email</label>
                        <input type="email" name="email" required autocomplete="email"></div>
                    <button type="submit" class="btn btn-primary btn-lg w-full">Send reset link</button>
                </form>
            <?php endif; ?>
            <p class="muted small mt-16"><a href="<?= e(url('auth/login.php')) ?>">← Back to sign in</a></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
