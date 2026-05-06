<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Terms of service — Matendo Medics';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Legal · terms</span>
            <h1>Terms of service</h1>
            <p class="lede">Last updated: <?= date('F j, Y') ?></p>
        </div>
        <div class="hero-art is-mark" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/matendo-mark.svg')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <p>Welcome to Matendo Medics. By using this platform you agree to these terms. Use the service lawfully, do not misrepresent credentials, and pay invoices on time. We may suspend accounts that breach these rules. Disputes are governed by the laws of Uganda.</p>
        <p>Specific engagements between clients and professionals are also governed by individual contracts signed through the platform. The platform is not itself a healthcare provider.</p>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
