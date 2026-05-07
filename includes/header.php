<?php
/** @var string $pageTitle */
/** @var string|null $pageDescription */
/** @var string|null $activeNav */
$pageTitle       = $pageTitle       ?? 'Matendo Medics';
$pageDescription = $pageDescription ?? 'Matendo Medics connects healthcare facilities and patients with vetted medical professionals.';
$activeNav       = $activeNav       ?? '';
$user = current_user();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:image" content="<?= e(asset('images/matendo-logo.svg')) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/svg+xml" href="<?= e(asset('images/matendo-mark.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/pages.css')) ?>">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <meta name="csrf-token" content="<?= e(csrf()) ?>">
    <meta name="api-base" content="<?= e(url('api/')) ?>">
</head>
<body data-page="<?= e($activeNav) ?>">

<a href="#main" class="skip-link">Skip to main content</a>

<header class="nav-container" id="navContainer">
    <div class="nav-content">
        <a href="<?= e(url('index.php')) ?>" class="logo-link" aria-label="Matendo home">
            <img src="<?= e(asset('images/matendo-logo.svg')) ?>" alt="Matendo Medics" class="logo-img">
        </a>

        <nav class="nav-links" aria-label="Primary">
            <a href="<?= e(url('index.php'))   ?>" class="<?= $activeNav === 'home'    ? 'active' : '' ?>">Home</a>
            <a href="<?= e(url('hire.php'))    ?>" class="<?= $activeNav === 'hire'    ? 'active' : '' ?>">Hire</a>
            <a href="<?= e(url('talent.php'))  ?>" class="<?= $activeNav === 'talent'  ? 'active' : '' ?>">Find Talent</a>
            <a href="<?= e(url('join.php'))    ?>" class="<?= $activeNav === 'join'    ? 'active' : '' ?>">Join</a>
            <a href="<?= e(url('about.php'))   ?>" class="<?= $activeNav === 'about'   ? 'active' : '' ?>">About</a>
            <a href="<?= e(url('careers.php')) ?>" class="<?= $activeNav === 'careers' ? 'active' : '' ?>">Careers</a>
            <a href="<?= e(url('contact.php')) ?>" class="<?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a>
        </nav>

        <div class="nav-cta">
            <?php if ($user): ?>
                <a href="<?= e(url('auth/dashboard.php')) ?>" class="btn btn-ghost">Dashboard</a>
                <a href="<?= e(url('auth/logout.php')) ?>" class="btn btn-outline">Sign out</a>
            <?php else: ?>
                <button type="button" class="btn btn-ghost"   data-open-modal="loginModal">Sign in</button>
                <button type="button" class="btn btn-primary" data-open-modal="registerModal">Get started</button>
            <?php endif; ?>
        </div>

        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu" aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
    </div>
</header>

<!-- Mobile menu: lives OUTSIDE the .nav-container because the nav's
     backdrop-filter would otherwise become its containing block and
     collapse the menu to header height. -->
<div class="mobile-menu" id="mobileMenu" hidden>
    <a href="<?= e(url('index.php'))   ?>" class="<?= $activeNav === 'home'    ? 'active' : '' ?>">Home</a>
    <a href="<?= e(url('hire.php'))    ?>" class="<?= $activeNav === 'hire'    ? 'active' : '' ?>">Hire</a>
    <a href="<?= e(url('talent.php'))  ?>" class="<?= $activeNav === 'talent'  ? 'active' : '' ?>">Find Talent</a>
    <a href="<?= e(url('join.php'))    ?>" class="<?= $activeNav === 'join'    ? 'active' : '' ?>">Join</a>
    <a href="<?= e(url('about.php'))   ?>" class="<?= $activeNav === 'about'   ? 'active' : '' ?>">About</a>
    <a href="<?= e(url('careers.php')) ?>" class="<?= $activeNav === 'careers' ? 'active' : '' ?>">Careers</a>
    <a href="<?= e(url('contact.php')) ?>" class="<?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a>
    <?php if ($user): ?>
        <a href="<?= e(url('auth/dashboard.php')) ?>">Dashboard</a>
        <a href="<?= e(url('auth/logout.php')) ?>">Sign out</a>
    <?php else: ?>
        <button type="button" class="mobile-menu-cta" data-open-modal="loginModal">Sign in</button>
        <button type="button" class="mobile-menu-cta" data-open-modal="registerModal">Get started</button>
    <?php endif; ?>
</div>

<?php if (!$user): ?>
<!-- Sign-in modal -->
<div class="modal modal-auth" id="loginModal" hidden role="dialog" aria-modal="true" aria-labelledby="loginTitle">
    <div class="modal-card modal-card-sm">
        <header>
            <h3 id="loginTitle">Welcome back</h3>
            <button class="modal-close" aria-label="Close" data-close-modal>&times;</button>
        </header>
        <form id="loginForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
            <div class="field">
                <label for="loginEmail">Email</label>
                <input id="loginEmail" type="email" name="email" required autocomplete="email">
            </div>
            <div class="field">
                <label for="loginPassword">Password</label>
                <input id="loginPassword" type="password" name="password" required minlength="8" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-full">Sign in</button>
            <div class="form-feedback" data-feedback hidden></div>
            <p class="muted small text-center mt-16">
                New to Matendo?
                <button type="button" class="linklike" data-close-modal data-open-modal="registerModal">Create an account</button>
            </p>
        </form>
    </div>
</div>

<!-- Register modal -->
<div class="modal modal-auth" id="registerModal" hidden role="dialog" aria-modal="true" aria-labelledby="registerTitle">
    <div class="modal-card modal-card-sm">
        <header>
            <h3 id="registerTitle">Create your account</h3>
            <button class="modal-close" aria-label="Close" data-close-modal>&times;</button>
        </header>
        <form id="registerForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
            <div class="grid-2">
                <div class="field">
                    <label for="regFirst">First name</label>
                    <input id="regFirst" type="text" name="firstName" required minlength="2" autocomplete="given-name">
                </div>
                <div class="field">
                    <label for="regLast">Last name</label>
                    <input id="regLast" type="text" name="lastName" required minlength="2" autocomplete="family-name">
                </div>
            </div>
            <div class="field">
                <label for="regEmail">Email</label>
                <input id="regEmail" type="email" name="email" required autocomplete="email">
            </div>
            <div class="field">
                <label for="regPassword">Password (min 10 chars)</label>
                <input id="regPassword" type="password" name="password" required minlength="10" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="regRole">I am a…</label>
                <select id="regRole" name="role" required>
                    <option value="client">Patient / individual seeking care</option>
                    <option value="facility">Healthcare facility</option>
                    <option value="professional">Healthcare professional</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-full">Create account</button>
            <div class="form-feedback" data-feedback hidden></div>
            <p class="muted small text-center mt-16">
                Already have an account?
                <button type="button" class="linklike" data-close-modal data-open-modal="loginModal">Sign in</button>
            </p>
        </form>
    </div>
</div>
<?php endif; ?>

<main id="main">
