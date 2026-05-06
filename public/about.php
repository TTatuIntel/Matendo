<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'About Matendo Medics';
$pageDescription = 'We bridge the gap between healthcare facilities and medical professionals, with reliable staffing and flexibility for clinicians.';
$activeNav = 'about';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero hero-compact">
    <div class="hero-content">
        <h1>Bridging facilities and clinicians.</h1>
        <p class="lede">We deliver reliable staff to the facilities that need them, while giving healthcare professionals the flexibility, will and choice to manage their careers without burnout.</p>
    </div>
</section>

<section class="section">
    <div class="container two-col">
        <div>
            <h2>Our mission</h2>
            <p>Simplify and elevate the healthcare experience for medical professionals, facilities and patients. We deliver tailored solutions that meet the unique needs of each group, ensuring seamless operations and satisfaction at every step.</p>
        </div>
        <div>
            <h2>Our vision</h2>
            <p>To be a leading force in transforming healthcare by empowering professionals, facilities and patients with innovative, efficient and compassionate solutions â€” an ecosystem where care is streamlined and outcomes improve for everyone.</p>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Our core values</h2>
        <div class="features-grid">
            <article class="feature-card"><i class="fas fa-shield-alt"></i><h3>Accountability</h3><p>We hold ourselves to the highest standards of care and continuous improvement.</p></article>
            <article class="feature-card"><i class="fas fa-bolt"></i><h3>Efficiency &amp; reliability</h3><p>Streamlined processes that minimise errors and reduce friction for providers.</p></article>
            <article class="feature-card"><i class="fas fa-users"></i><h3>Collaboration</h3><p>Strong, long-lasting partnerships with clinicians, facilities and patients.</p></article>
            <article class="feature-card"><i class="fas fa-award"></i><h3>Excellence</h3><p>Patient-centred, professional service that exceeds expectations.</p></article>
            <article class="feature-card"><i class="fas fa-comment-dots"></i><h3>Transparency</h3><p>Open communication that builds trust with every stakeholder.</p></article>
            <article class="feature-card"><i class="fas fa-globe"></i><h3>Respect for diversity</h3><p>Inclusive, culturally competent solutions for every community we serve.</p></article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Our impact</h2>
        <div class="stats-grid">
            <div class="stat"><i class="fas fa-user-md"></i><strong>15,000+</strong><span>Healthcare professionals</span></div>
            <div class="stat"><i class="fas fa-hospital"></i><strong>5,000+</strong><span>Partner facilities</span></div>
            <div class="stat"><i class="fas fa-map-marker-alt"></i><strong>50+</strong><span>Districts covered</span></div>
            <div class="stat"><i class="fas fa-thumbs-up"></i><strong>98%</strong><span>Satisfaction rate</span></div>
        </div>
    </div>
</section>

<section class="section cta">
    <div class="container text-center">
        <h2>Join our growing community</h2>
        <p>Whether you are a healthcare professional looking for your next opportunity or a facility seeking qualified staff, we are here to help you succeed.</p>
        <div class="hero-actions">
            <a href="<?= e(url('join.php')) ?>" class="btn btn-primary">Join as a professional</a>
            <a href="<?= e(url('hire.php')) ?>" class="btn btn-outline">Hire talent</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
