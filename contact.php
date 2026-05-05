<?php
require_once __DIR__ . '/config/bootstrap.php';
$pageTitle = 'Contact Matendo Medics';
$pageDescription = 'Reach out to our team for support, partnerships, or general inquiries. We respond within 24 hours.';
$activeNav = 'contact';
include __DIR__ . '/includes/header.php';
?>
<section class="hero hero-compact">
    <div class="hero-content">
        <h1>Get in touch</h1>
        <p class="lede">Questions, partnerships, or platform support — our team replies within one business day.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <article class="contact-card">
                <i class="fas fa-headset"></i>
                <h3>Customer support</h3>
                <p>Available 24/7 for active engagements.</p>
                <a href="tel:+256781053105"><i class="fas fa-phone"></i> +256 781 053 105</a>
            </article>
            <article class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email us</h3>
                <p>We respond within 24 hours.</p>
                <a href="mailto:info@matendo.com"><i class="fas fa-envelope"></i> info@matendo.com</a>
            </article>
            <article class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Office</h3>
                <p>Visit us by appointment.</p>
                <a href="https://maps.google.com/?q=Kampala,Uganda" rel="noopener"><i class="fas fa-map-pin"></i> Kampala, Uganda</a>
            </article>
        </div>

        <div class="two-col mt-32">
            <div class="card">
                <h2>Send a message</h2>
                <form id="contactForm" novalidate>
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" required minlength="2" maxlength="120">
                    </div>
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" required>
                    </div>
                    <div class="field">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required minlength="10" maxlength="5000"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send message</button>
                    <div class="form-feedback" data-feedback hidden></div>
                </form>
            </div>

            <div class="card">
                <h2>Frequently asked</h2>
                <details class="faq" open><summary>What are your business hours?</summary><p>Support is 24/7. Office hours are Monday–Friday, 9:00–18:00 EAT.</p></details>
                <details class="faq"><summary>How quickly can I expect a response?</summary><p>Within 24 hours during business days; usually faster.</p></details>
                <details class="faq"><summary>Do you offer technical support?</summary><p>Yes — our platform team handles account, billing and integration questions.</p></details>
                <details class="faq"><summary>Is my health information secure?</summary><p>Yes. PHI fields are encrypted at rest with AES-256-GCM. See our <a href="<?= e(url('legal/security.php')) ?>">security page</a>.</p></details>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
