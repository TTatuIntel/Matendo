<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Security & HIPAA practices — Matendo Medics';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Trust · security</span>
            <h1>Security &amp; HIPAA-equivalent practices</h1>
            <p class="lede">How we protect your data — at rest, in transit, and in our process.</p>
        </div>
        <div class="hero-art is-mark" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/matendo-mark.svg')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <ul>
            <li><strong>Encryption in transit:</strong> TLS 1.2+ everywhere; HSTS in production.</li>
            <li><strong>Encryption at rest:</strong> Sensitive PHI columns (medical conditions, medications, allergies) are encrypted with AES-256-GCM at the application layer.</li>
            <li><strong>Authentication:</strong> Argon2id password hashing with a server-side pepper; account lockouts after 5 failed attempts.</li>
            <li><strong>Application security:</strong> CSRF tokens on every form, strict file-type validation, rate limiting per IP and route, parameterised queries.</li>
            <li><strong>Audit logging:</strong> Privileged actions are written to an append-only audit trail.</li>
            <li><strong>Least privilege:</strong> The web app connects to MySQL with a dedicated user that has only DML rights — no schema or admin permissions.</li>
        </ul>
        <p>Report a vulnerability: <a href="mailto:security@matendo.com">security@matendo.com</a>.</p>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
