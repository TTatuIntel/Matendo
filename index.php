
<?php
// DEBUG BLOCK: Show PHP errors and confirm execution
error_reporting(E_ALL); ini_set('display_errors', 1);
echo '<div style="background:#ffe0e0;color:#900;padding:8px 16px;font-size:1.1em;z-index:9999;position:relative;">PHP is running. Timestamp: '.date('Y-m-d H:i:s').'</div>';

require_once __DIR__ . '/config/bootstrap.php';
$pageTitle       = 'Matendo Medics — Hire vetted medical professionals';
$pageDescription = 'Matendo connects healthcare facilities and patients with the top tier of vetted doctors, nurses and caregivers across East Africa.';
$activeNav       = 'home';

// Featured talent (live data; fallback to seeds if table empty).
try {
    $featured = DB::pdo()->query("
        SELECT first_name, last_name, profession, specialization, avatar_path, rating_avg
        FROM professionals WHERE status = 'active' ORDER BY rating_avg DESC LIMIT 6
    ")->fetchAll();
} catch (Throwable $e) {
    echo '<div style="background:#ffe0e0;color:#900;padding:8px 16px;">DB ERROR: '.e($e->getMessage()).'</div>';
    $featured = [];
}

if (!$featured) {
    $featured = [
        ['first_name'=>'Sarah','last_name'=>'Williams','profession'=>'General Practice','specialization'=>'Family medicine','avatar_path'=>'images/doc1.png','rating_avg'=>4.9],
        ['first_name'=>'James','last_name'=>'Smith','profession'=>'Cardiology','specialization'=>'Interventional cardiology','avatar_path'=>'images/doc2.png','rating_avg'=>4.8],
        ['first_name'=>'Emily','last_name'=>'Brown','profession'=>'Pediatrics','specialization'=>'Neonatal care','avatar_path'=>'images/doc3.png','rating_avg'=>4.9],
        ['first_name'=>'Mark','last_name'=>'Johnson','profession'=>'Orthopedics','specialization'=>'Sports injury','avatar_path'=>'images/doc4.png','rating_avg'=>4.7],
        ['first_name'=>'Sophia','last_name'=>'Chen','profession'=>'Neurology','specialization'=>'Stroke care','avatar_path'=>'images/doc5.png','rating_avg'=>4.9],
        ['first_name'=>'Robert','last_name'=>'Taylor','profession'=>'Oncology','specialization'=>'Radiation oncology','avatar_path'=>'images/doc6.png','rating_avg'=>4.8],
    ];
}

include __DIR__ . '/includes/header.php';
echo '<div style="background:#e0ffe0;color:#070;padding:8px 16px;font-size:1.1em;z-index:9999;position:relative;">Reached before hero section</div>';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Vetted medical talent · 24h match</span>
            <h1>Hire the top tier of medical professionals.</h1>
            <p class="lede">
                Matendo gives healthcare facilities, hospitals and individual patients fast, reliable access to certified
                doctors, nurses and caregivers — screened, licensed, and ready to start.
            </p>
            <div class="hero-actions">
                <a href="<?= e(url('hire.php')) ?>"   class="btn btn-primary btn-lg">Hire talent</a>
                <a href="<?= e(url('join.php')) ?>"   class="btn btn-outline btn-lg">Join as a professional</a>
                <a href="<?= e(url('talent.php')) ?>" class="btn btn-ghost btn-lg">Browse the network →</a>
            </div>
            <ul class="trust-bar">
                <li><strong>15,000+</strong> vetted professionals</li>
                <li><strong>5,000+</strong> partner facilities</li>
                <li><strong>98%</strong> client satisfaction</li>
            </ul>
        </div>
        <div class="hero-image" data-reveal="right">
            <img src="<?= e(asset('images/doc1.png')) ?>" alt="" loading="eager" decoding="async">
        </div>
    </div>
</section>

<section class="brand-bar">
    <div class="container">
        <p class="label">Trusted by leading hospitals &amp; clinics</p>
        <ul>
            <li>Mulago&nbsp;Hospital</li>
            <li>Aga&nbsp;Khan</li>
            <li>Nakasero</li>
            <li>IHK</li>
            <li>UMC&nbsp;Victoria</li>
            <li>Case&nbsp;Med&nbsp;Centre</li>
        </ul>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title" data-reveal>Why facilities choose Matendo</h2>
        <p class="section-sub" data-reveal>Built for healthcare. Designed to remove every friction between a need and the right professional.</p>
        <div class="features-grid" data-reveal-stagger>
            <article class="feature-card">
                <i class="fas fa-user-shield"></i>
                <h3>Rigorously vetted</h3>
                <p>Every professional passes credential verification, license checks, and a clinical interview before joining.</p>
            </article>
            <article class="feature-card">
                <i class="fas fa-clock"></i>
                <h3>Matched within 24 hours</h3>
                <p>Submit your need and we hand-pick a shortlist of three matches the next day, not next month.</p>
            </article>
            <article class="feature-card">
                <i class="fas fa-handshake"></i>
                <h3>Flexible engagements</h3>
                <p>Full-time, part-time, locum, or single-shift. Hire how you need, scale when you grow.</p>
            </article>
            <article class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Risk-free trial</h3>
                <p>If the first week isn't a fit, we re-match at no cost. You only pay for time that delivered value.</p>
            </article>
        </div>
    </div>
</section>


<section class="section section-alt">
    <div class="container">
        <h2 class="section-title" data-reveal>Featured medical professionals</h2>
        <p class="section-sub" data-reveal>Browse by specialty. Sign in to access the full directory and contact details.</p>
        <div class="talent-tabs" role="tablist" aria-label="Talent specialties">
            <button type="button" class="talent-tab is-active" data-talent-tab="all" aria-selected="true" role="tab" id="tab-all">All</button>
            <button type="button" class="talent-tab" data-talent-tab="doctor" role="tab" id="tab-doctor">Doctors</button>
            <button type="button" class="talent-tab" data-talent-tab="nurse" role="tab" id="tab-nurse">Nurses</button>
            <button type="button" class="talent-tab" data-talent-tab="therapist" role="tab" id="tab-therapist">Therapists</button>
        </div>
        <div class="talent-panels">
            <div class="talent-grid" data-talent-panel="all" data-reveal-stagger role="tabpanel" aria-labelledby="tab-all">
                <?php foreach ($featured as $p): ?>
                    <article class="talent-card">
                        <div class="talent-photo">
                            <span class="verified-badge"><i class="fas fa-circle-check"></i> Verified</span>
                            <img src="<?= e(asset($p['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" loading="lazy">
                        </div>
                        <div class="talent-info">
                            <h3>Dr. <?= e($p['first_name'].' '.$p['last_name']) ?></h3>
                            <p class="talent-specialty"><?= e($p['profession']) ?></p>
                            <p class="talent-detail"><?= e($p['specialization'] ?? '') ?></p>
                            <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$p['rating_avg'], 1) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="talent-grid" data-talent-panel="doctor" hidden data-reveal-stagger role="tabpanel" aria-labelledby="tab-doctor">
                <?php foreach ($featured as $p): if (stripos($p['profession'], 'doctor') !== false || stripos($p['profession'], 'general') !== false || stripos($p['profession'], 'cardio') !== false || stripos($p['profession'], 'neuro') !== false || stripos($p['profession'], 'pedia') !== false || stripos($p['profession'], 'onco') !== false || stripos($p['profession'], 'ortho') !== false): ?>
                    <article class="talent-card">
                        <div class="talent-photo">
                            <span class="verified-badge"><i class="fas fa-circle-check"></i> Verified</span>
                            <img src="<?= e(asset($p['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" loading="lazy">
                        </div>
                        <div class="talent-info">
                            <h3>Dr. <?= e($p['first_name'].' '.$p['last_name']) ?></h3>
                            <p class="talent-specialty"><?= e($p['profession']) ?></p>
                            <p class="talent-detail"><?= e($p['specialization'] ?? '') ?></p>
                            <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$p['rating_avg'], 1) ?></p>
                        </div>
                    </article>
                <?php endif; endforeach; ?>
            </div>
            <div class="talent-grid" data-talent-panel="nurse" hidden data-reveal-stagger role="tabpanel" aria-labelledby="tab-nurse">
                <?php foreach ($featured as $p): if (stripos($p['profession'], 'nurse') !== false): ?>
                    <article class="talent-card">
                        <div class="talent-photo">
                            <span class="verified-badge"><i class="fas fa-circle-check"></i> Verified</span>
                            <img src="<?= e(asset($p['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" loading="lazy">
                        </div>
                        <div class="talent-info">
                            <h3>Dr. <?= e($p['first_name'].' '.$p['last_name']) ?></h3>
                            <p class="talent-specialty"><?= e($p['profession']) ?></p>
                            <p class="talent-detail"><?= e($p['specialization'] ?? '') ?></p>
                            <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$p['rating_avg'], 1) ?></p>
                        </div>
                    </article>
                <?php endif; endforeach; ?>
            </div>
            <div class="talent-grid" data-talent-panel="therapist" hidden data-reveal-stagger role="tabpanel" aria-labelledby="tab-therapist">
                <?php foreach ($featured as $p): if (stripos($p['profession'], 'therap') !== false): ?>
                    <article class="talent-card">
                        <div class="talent-photo">
                            <span class="verified-badge"><i class="fas fa-circle-check"></i> Verified</span>
                            <img src="<?= e(asset($p['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" loading="lazy">
                        </div>
                        <div class="talent-info">
                            <h3>Dr. <?= e($p['first_name'].' '.$p['last_name']) ?></h3>
                            <p class="talent-specialty"><?= e($p['profession']) ?></p>
                            <p class="talent-detail"><?= e($p['specialization'] ?? '') ?></p>
                            <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$p['rating_avg'], 1) ?></p>
                        </div>
                    </article>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <div class="text-center"><a href="<?= e(url('talent.php')) ?>" class="btn btn-outline">Browse all talent →</a></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title" data-reveal>How hiring on Matendo works</h2>
        <ol class="steps" data-reveal-stagger>
            <li><span class="step-num">1</span><h3>Tell us your need</h3><p>Choose facility hiring or personal home care and describe the role.</p></li>
            <li><span class="step-num">2</span><h3>Get a curated shortlist</h3><p>Within 24 hours we send 3 vetted professionals matched to your brief.</p></li>
            <li><span class="step-num">3</span><h3>Interview &amp; select</h3><p>Message and schedule interviews directly through the platform.</p></li>
            <li><span class="step-num">4</span><h3>Hire &amp; manage</h3><p>Sign contracts, approve timesheets and pay through Matendo's escrow.</p></li>
        </ol>
        <div class="text-center" data-reveal><a href="<?= e(url('hire.php')) ?>" class="btn btn-primary btn-lg">Start hiring now</a></div>
    </div>
</section>

<section class="testimonials section-alt">
    <div class="container" data-reveal>
        <h2 class="section-title">Loved by facilities and patients</h2>
        <p class="section-sub">Real outcomes from teams who trusted Matendo with critical roles.</p>
    </div>
    <div class="testimonial-track">
        <?php
        $testimonials = [
            ['quote' => 'We filled two ICU nurse roles in 36 hours. The vetting saved us weeks of interviews.', 'name' => 'Dr. Aisha Nansubuga', 'role' => 'Medical Director, Kampala', 'avatar' => 'images/doc3.png'],
            ['quote' => 'The home-care nurse Matendo matched for my mother was professional and warm. Five stars.', 'name' => 'Peter Okello', 'role' => 'Family client', 'avatar' => 'images/doc2.png'],
            ['quote' => 'Best locum platform we have used. Transparent pricing, vetted candidates, fast match.', 'name' => 'HR Lead', 'role' => 'Aga Khan Hospital', 'avatar' => 'images/doc4.png'],
            ['quote' => 'I joined as a professional 3 months ago and my schedule is full. Game changer.', 'name' => 'Dr. Sophia Chen', 'role' => 'Neurologist', 'avatar' => 'images/doc5.png'],
            ['quote' => 'Compliance, escrow and timesheets in one place. Finance team finally happy.', 'name' => 'Operations Manager', 'role' => 'Nakasero Hospital', 'avatar' => 'images/doc6.png'],
        ];
        foreach (array_merge($testimonials, $testimonials) as $t): ?>
            <article class="testimonial-card">
                <p class="quote"><?= e($t['quote']) ?></p>
                <div class="testimonial-author">
                    <img src="<?= e(asset($t['avatar'])) ?>" alt="" loading="lazy">
                    <div>
                        <strong><?= e($t['name']) ?></strong>
                        <span><?= e($t['role']) ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-alt" id="personal-care">
    <div class="container">
        <h2 class="section-title" data-reveal>Personal healthcare support, at home</h2>
        <p class="section-sub" data-reveal>Bring vetted nurses, therapists and caregivers to your loved ones &mdash; on your schedule.</p>
        <div class="features-grid" data-reveal-stagger>
            <article class="feature-card">
                <i class="fas fa-user-nurse"></i>
                <h3>Home care services</h3>
                <ul class="check-list">
                    <li><i class="fas fa-check"></i> Post-surgery recovery</li>
                    <li><i class="fas fa-check"></i> Elderly companionship</li>
                    <li><i class="fas fa-check"></i> Medication management</li>
                </ul>
            </article>
            <article class="feature-card">
                <i class="fas fa-heartbeat"></i>
                <h3>Specialised care</h3>
                <ul class="check-list">
                    <li><i class="fas fa-check"></i> Physical therapy</li>
                    <li><i class="fas fa-check"></i> Chronic conditions</li>
                    <li><i class="fas fa-check"></i> Rehabilitation</li>
                </ul>
            </article>
            <article class="feature-card">
                <i class="fas fa-calendar-check"></i>
                <h3>Flexible scheduling</h3>
                <ul class="check-list">
                    <li><i class="fas fa-check"></i> Single-visit bookings</li>
                    <li><i class="fas fa-check"></i> Weekly recurring visits</li>
                    <li><i class="fas fa-check"></i> 24/7 emergency cover</li>
                </ul>
            </article>
        </div>
        <div class="text-center"><a href="<?= e(url('hire.php#care')) ?>" class="btn btn-primary">Find personal care</a></div>
    </div>
</section>

<section class="section cta">
    <div class="container text-center" data-reveal="zoom">
        <h2>Ready to staff smarter?</h2>
        <p>Tell us what you need. We'll send a curated shortlist within 24 hours &mdash; no upfront cost.</p>
        <div class="hero-actions" style="justify-content:center">
            <a href="<?= e(url('hire.php')) ?>" class="btn btn-primary btn-lg">Start hiring</a>
            <a href="<?= e(url('join.php')) ?>" class="btn btn-outline btn-lg">I'm a professional</a>
        </div>
    </div>
</section>

<section class="section newsletter">
    <div class="container" data-reveal>
        <h2 class="section-title">Stay in the loop</h2>
        <p class="section-sub">Healthcare staffing insights, new openings and product updates &mdash; once a month, no spam.</p>
        <form id="newsletterForm" class="inline-form" novalidate>
            <?= csrf_field() ?>
            <label class="sr-only" for="nlEmail">Email address</label>
            <input id="nlEmail" type="email" name="email" placeholder="you@example.com" required>
            <button type="submit" class="btn btn-primary">Subscribe</button>
        </form>
        <p class="muted small text-center">We respect your privacy. Unsubscribe at any time.</p>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
