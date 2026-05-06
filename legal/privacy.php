<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Privacy policy — Matendo Medics';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero hero-compact">
    <div class="hero-content" data-reveal>
        <span class="eyebrow">Legal · privacy</span>
        <h1>Privacy policy</h1>
        <p class="lede">Last updated: <?= date('F j, Y') ?></p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <p>Matendo Medics processes personal information to operate the staffing platform, match clients with professionals, and meet our legal obligations under Uganda's Data Protection and Privacy Act, the EU GDPR (where applicable), and HIPAA-equivalent practices for protected health information.</p>
        <h2>What we collect</h2>
        <ul>
            <li>Contact details (name, email, phone, address)</li>
            <li>Professional credentials (licenses, certifications, work history)</li>
            <li>Service requests including, for personal-care requests, sensitive health information that we encrypt at rest using AES-256-GCM</li>
            <li>Technical telemetry (IP, user agent) for security and abuse prevention</li>
        </ul>
        <h2>How we use it</h2>
        <p>To deliver the service, communicate with you, verify professionals, prevent fraud, and as required by law. We do not sell personal data.</p>
        <h2>Your rights</h2>
        <p>Access, correction, deletion, portability, and the right to lodge a complaint with your data protection authority. Contact <a href="mailto:privacy@matendo.com">privacy@matendo.com</a>.</p>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
