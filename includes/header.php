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
    <meta property="og:image" content="<?= e(asset('images/matelogo1.png')) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="<?= e(asset('images/matelogo1.png')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/pages.css')) ?>">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQDuqOeY7/EgfytaMlb"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <meta name="csrf-token" content="<?= e(csrf()) ?>">
</head>
<body data-page="<?= e($activeNav) ?>">

<a href="#main" class="skip-link">Skip to main content</a>

<header class="nav-container" id="navContainer">
    <div class="nav-content">
        <a href="<?= e(url('index.php')) ?>" class="logo-link" aria-label="Matendo home">
            <img src="<?= e(asset('images/matelogo1.png')) ?>" alt="Matendo Medics" class="logo-img">
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
                <a href="<?= e(url('auth/login.php'))    ?>" class="btn btn-ghost">Sign in</a>
                <a href="<?= e(url('auth/register.php')) ?>" class="btn btn-primary">Get started</a>
            <?php endif; ?>
        </div>

        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu" aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu" hidden>
        <a href="<?= e(url('index.php'))   ?>">Home</a>
        <a href="<?= e(url('hire.php'))    ?>">Hire</a>
        <a href="<?= e(url('talent.php'))  ?>">Find Talent</a>
        <a href="<?= e(url('join.php'))    ?>">Join</a>
        <a href="<?= e(url('about.php'))   ?>">About</a>
        <a href="<?= e(url('careers.php')) ?>">Careers</a>
        <a href="<?= e(url('contact.php')) ?>">Contact</a>
        <?php if ($user): ?>
            <a href="<?= e(url('auth/dashboard.php')) ?>">Dashboard</a>
            <a href="<?= e(url('auth/logout.php')) ?>">Sign out</a>
        <?php else: ?>
            <a href="<?= e(url('auth/login.php'))    ?>">Sign in</a>
            <a href="<?= e(url('auth/register.php')) ?>">Get started</a>
        <?php endif; ?>
    </div>
</header>

<main id="main">
