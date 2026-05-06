<?php
require_once __DIR__ . '/../config/bootstrap.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::requireCsrf();
    Security::rateLimit('auth-login', 10);

    $email    = trim((string)($_POST['email']    ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = DB::pdo()->prepare("SELECT * FROM users WHERE email = :e AND status = 'active' LIMIT 1");
        $stmt->execute([':e' => $email]);
        $u = $stmt->fetch();

        if ($u && !empty($u['locked_until']) && strtotime($u['locked_until']) > time()) {
            $error = 'Account temporarily locked. Try again later.';
        } elseif ($u && password_verify($password . (Env::get('APP_PEPPER', '') ?? ''), $u['password_hash'])) {
            session_regenerate_id(true);
            Security::rotateCsrf();
            $_SESSION['user'] = [
                'id'    => (int)$u['id'],
                'email' => $u['email'],
                'role'  => $u['role'],
                'name'  => trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')),
            ];
            DB::pdo()->prepare("UPDATE users SET last_login_at = NOW(), failed_logins = 0 WHERE id = :id")
                ->execute([':id' => $u['id']]);
            redirect(url('auth/dashboard.php'));
        } else {
            if ($u) {
                DB::pdo()->prepare("UPDATE users SET failed_logins = failed_logins + 1,
                    locked_until = IF(failed_logins + 1 >= 5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), locked_until)
                    WHERE id = :id")->execute([':id' => $u['id']]);
            }
            $error = 'Email or password is incorrect.';
        }
    }
}

$pageTitle = 'Sign in — Matendo Medics';
$activeNav = '';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Account · sign in</span>
            <h1>Welcome back</h1>
            <p class="lede">Sign in to access your Matendo dashboard.</p>
        </div>
        <div class="hero-art is-mark" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/matendo-mark.svg')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section auth-page">
    <div class="container narrow">
        <div class="card">

            <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>

            <form method="POST" novalidate>
                <?= csrf_field() ?>
                <div class="field"><label>Email</label>
                    <input type="email" name="email" required autocomplete="email"></div>
                <div class="field"><label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password" minlength="8"></div>
                <button type="submit" class="btn btn-primary btn-lg w-full">Sign in</button>
            </form>

            <p class="muted small mt-16">
                New to Matendo? <a href="<?= e(url('auth/register.php')) ?>">Create an account</a><br>
                Forgot password? <a href="<?= e(url('auth/forgot.php')) ?>">Reset it here</a>
            </p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
