<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Find medical talent â€” Matendo Medics';
$pageDescription = 'Search vetted doctors, nurses, lab technicians, pharmacists and caregivers. Filter by profession, location and experience.';
$activeNav = 'talent';

$q          = trim((string)($_GET['q']          ?? ''));
$profession = trim((string)($_GET['profession'] ?? ''));
$location   = trim((string)($_GET['location']   ?? ''));
$minYears   = (int)($_GET['minYears'] ?? 0);
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 12;
$offset     = ($page - 1) * $perPage;

$where = ["status IN ('active','approved')"];
$params = [];

if ($q !== '') {
    $where[]       = "(MATCH(first_name,last_name,headline,bio,specialization) AGAINST (:q IN BOOLEAN MODE)
                       OR first_name LIKE :like OR last_name LIKE :like OR specialization LIKE :like)";
    $params[':q']    = $q;
    $params[':like'] = '%' . $q . '%';
}
if ($profession !== '') { $where[] = "profession = :prof"; $params[':prof'] = $profession; }
if ($location   !== '') { $where[] = "(preferred_location LIKE :loc OR location LIKE :loc)"; $params[':loc'] = '%' . $location . '%'; }
if ($minYears   > 0)    { $where[] = "years_experience >= :ye"; $params[':ye'] = $minYears; }

$sql = "
    SELECT id, reference_number, first_name, last_name, profession, specialization,
           preferred_location, years_experience, rating_avg, rating_count, headline, avatar_path
    FROM professionals
    WHERE " . implode(' AND ', $where) . "
    ORDER BY rating_avg DESC, years_experience DESC
    LIMIT :off, :lim
";

try {
    $stmt = DB::pdo()->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('talent search failed: ' . $e->getMessage());
    $results = [];
}

$professions = ['Doctor','Nurse / Midwife','Pharmacist','Lab Technologist','Radiographer','Caregiver'];
include __DIR__ . '/../includes/header.php';
?>
<section class="hero hero-compact">
    <div class="hero-content">
        <h1>Find medical talent</h1>
        <p class="lede">Browse the Matendo network. Every profile is screened, licensed and ready to engage.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form method="GET" class="search-bar" role="search" aria-label="Search talent">
            <div class="field">
                <label class="sr-only" for="q">Keyword</label>
                <input id="q" name="q" type="search" placeholder="Search by name, skill, specialtyâ€¦" value="<?= e($q) ?>">
            </div>
            <div class="field">
                <label class="sr-only" for="profession">Profession</label>
                <select id="profession" name="profession">
                    <option value="">All professions</option>
                    <?php foreach ($professions as $p): ?>
                        <option value="<?= e($p) ?>" <?= $profession === $p ? 'selected' : '' ?>><?= e($p) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label class="sr-only" for="location">Location</label>
                <input id="location" name="location" type="text" placeholder="Location (e.g. Kampala)" value="<?= e($location) ?>">
            </div>
            <div class="field">
                <label class="sr-only" for="minYears">Min experience</label>
                <select id="minYears" name="minYears">
                    <option value="0">Any experience</option>
                    <?php foreach ([1,3,5,10] as $y): ?>
                        <option value="<?= $y ?>" <?= $minYears === $y ? 'selected' : '' ?>><?= $y ?>+ years</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (!$results): ?>
            <p class="muted text-center mt-32">No professionals match those filters yet. Try broadening the search, or <a href="<?= e(url('hire.php')) ?>">submit a hiring request</a> and we will source matches for you.</p>
        <?php else: ?>
            <div class="talent-grid">
                <?php foreach ($results as $p): ?>
                    <article class="talent-card">
                        <img src="<?= e(asset($p['avatar_path'] ?? 'images/doc1.png')) ?>" alt="" loading="lazy">
                        <div class="talent-info">
                            <h3>Dr. <?= e($p['first_name'].' '.$p['last_name']) ?></h3>
                            <p class="talent-specialty"><?= e($p['profession']) ?><?= $p['specialization'] ? ' Â· ' . e($p['specialization']) : '' ?></p>
                            <p class="talent-detail"><?= e($p['headline'] ?? '') ?></p>
                            <p class="muted small"><i class="fas fa-map-marker-alt"></i> <?= e($p['preferred_location'] ?? 'â€”') ?> Â· <?= (int)$p['years_experience'] ?> yrs experience</p>
                            <p class="rating"><i class="fas fa-star"></i> <?= number_format((float)$p['rating_avg'], 1) ?> <span class="muted small">(<?= (int)$p['rating_count'] ?>)</span></p>
                            <a class="btn btn-outline btn-sm" href="<?= e(url('professional.php?ref=' . urlencode($p['reference_number']))) ?>">View profile</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
