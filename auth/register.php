<?php
require_once __DIR__ . '/../config/bootstrap.php';

$error = null; $ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::requireCsrf();
    Security::rateLimit('auth-register', 5);

    $email    = trim((string)($_POST['email']    ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $first    = trim((string)($_POST['firstName'] ?? ''));
    $last     = trim((string)($_POST['lastName']  ?? ''));
    $role     = in_array($_POST['role'] ?? '', ['client','professional','facility'], true)
              ? $_POST['role'] : 'client';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $error = 'Invalid email.';
    elseif (mb_strlen($password) < 10)                $error = 'Password must be at least 10 characters.';
    elseif (mb_strlen($first) < 2 || mb_strlen($last) < 2) $error = 'Please enter your full name.';
    else {
        try {
            $hash = password_hash($password . (Env::get('APP_PEPPER', '') ?? ''), PASSWORD_ARGON2ID);
            DB::pdo()->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, role)
                VALUES (:e, :p, :f, :l, :r)
            ")->execute([':e' => $email, ':p' => $hash, ':f' => $first, ':l' => $last, ':r' => $role]);
            $ok = true;
        } catch (PDOException $e) {
            $error = $e->errorInfo[1] === 1062 ? 'Email already registered.' : 'Could not create account.';
        }
    }
}

$pageTitle = 'Create account — Matendo Medics';
include __DIR__ . '/../includes/header.php';
?>
<section class="section auth-page">
    <div class="container narrow">
        <div class="card">
            <h1>Create your account</h1>
            <p class="muted">Free to join. Verify your email after signup.</p>

            <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
            <?php if ($ok): ?>
                <div class="flash flash-success">Account created. <a href="<?= e(url('auth/login.php')) ?>">Sign in →</a></div>
            <?php else: ?>
                <form method="POST" novalidate>
                    <?= csrf_field() ?>
                    <div class="grid-2">
                        <div class="field"><label>First name</label>
                            <input type="text" name="firstName" required minlength="2" autocomplete="given-name"></div>
                        <div class="field"><label>Last name</label>
                            <input type="text" name="lastName" required minlength="2" autocomplete="family-name"></div>
                    </div>
                    <div class="field"><label>Email</label>
                        <input type="email" name="email" required autocomplete="email"></div>
                    <div class="field"><label>Password (min 10 chars)</label>
                        <input type="password" name="password" required minlength="10" autocomplete="new-password"></div>
                    <div class="field"><label>I am a…</label>
                        <select name="role" required>
                            <option value="client">Patient / individual seeking care</option>
                            <option value="facility">Healthcare facility</option>
                            <option value="professional">Healthcare professional</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-full">Create account</button>
                </form>
                <p class="muted small mt-16">Already have an account? <a href="<?= e(url('auth/login.php')) ?>">Sign in</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
