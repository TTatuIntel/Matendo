<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Careers at Matendo Medics';
$pageDescription = 'Join the team transforming healthcare staffing across East Africa.';
$activeNav = 'careers';

try {
    $jobs = DB::pdo()->query("
        SELECT title, employment, location, description, posted_at
        FROM career_postings WHERE is_open = 1 ORDER BY posted_at DESC
    ")->fetchAll();
} catch (Throwable $e) { $jobs = []; }

include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="hero-content hero-flip">
        <div class="hero-art" data-reveal="left" aria-hidden="true">
            <img src="<?= e(asset('images/team1.png')) ?>" alt="">
        </div>
        <div class="hero-text" data-reveal="right">
            <span class="eyebrow">Careers at Matendo</span>
            <h1>Join our team</h1>
            <p class="lede">We are looking for talented people who care about transforming healthcare staffing.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Current openings</h2>
        <?php if (!$jobs): ?>
            <p class="muted text-center">No openings right now. Check back soon, or <a href="<?= e(url('contact.php')) ?>">say hello</a>.</p>
        <?php else: ?>
            <div class="careers-grid">
                <?php foreach ($jobs as $j): ?>
                    <article class="career-card">
                        <header>
                            <h3><?= e($j['title']) ?></h3>
                            <span class="tag"><?= e($j['employment']) ?></span>
                        </header>
                        <ul class="meta">
                            <li><i class="fas fa-map-marker-alt"></i> <?= e($j['location']) ?></li>
                            <li><i class="fas fa-calendar-alt"></i> Posted <?= e(date('M j, Y', strtotime($j['posted_at']))) ?></li>
                        </ul>
                        <p><?= e($j['description']) ?></p>
                        <a class="btn btn-primary btn-sm" href="<?= e(url('join.php')) ?>">Apply now</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Our values</h2>
        <p class="section-sub">Core principles that shape everything we do.</p>
        <div class="features-grid">
            <article class="feature-card"><i class="fas fa-heart"></i><h3>Compassion</h3><p>We care deeply about clinicians and the patients they serve.</p></article>
            <article class="feature-card"><i class="fas fa-handshake"></i><h3>Integrity</h3><p>Honesty, transparency and ethical standards in every interaction.</p></article>
            <article class="feature-card"><i class="fas fa-lightbulb"></i><h3>Innovation</h3><p>Always looking for better ways to solve staffing challenges.</p></article>
            <article class="feature-card"><i class="fas fa-users"></i><h3>Collaboration</h3><p>The power of teamwork to create better outcomes for everyone.</p></article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Benefits &amp; perks</h2>
        <div class="features-grid">
            <article class="feature-card"><i class="fas fa-heartbeat"></i><h3>Health insurance</h3><p>Comprehensive medical, dental and vision cover.</p></article>
            <article class="feature-card"><i class="fas fa-coins"></i><h3>Competitive pay</h3><p>Compensation packages aligned with experience and impact.</p></article>
            <article class="feature-card"><i class="fas fa-calendar-alt"></i><h3>Flexible schedule</h3><p>Work-life balance is essential â€” we offer flexible scheduling.</p></article>
            <article class="feature-card"><i class="fas fa-graduation-cap"></i><h3>Development</h3><p>Continuous learning and clear growth pathways.</p></article>
            <article class="feature-card"><i class="fas fa-plane"></i><h3>Paid time off</h3><p>Generous leave and paid public holidays.</p></article>
            <article class="feature-card"><i class="fas fa-piggy-bank"></i><h3>Retirement</h3><p>Pension contributions to help you save for the future.</p></article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
