<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Hire medical professionals â€” Matendo Medics';
$pageDescription = 'Two ways to hire on Matendo: facility staffing and personal home care. Curated shortlist within 24 hours.';
$activeNav = 'hire';
include __DIR__ . '/../includes/header.php';

$today = date('Y-m-d');
?>
<section class="hero hero-compact">
    <div class="hero-content">
        <h1>Hire vetted medical professionals</h1>
        <p class="lede">Choose your hiring path. Submit a request and we will return a curated shortlist within 24 hours.</p>
    </div>
</section>

<section class="section" id="options">
    <div class="container">
        <div class="hire-options">
            <article class="hire-card">
                <i class="fas fa-hospital"></i>
                <h2>Facility hiring</h2>
                <p>Hospitals, clinics, labs and pharmacies â€” staff vacant roles or scale fast.</p>
                <ul class="check-list">
                    <li><i class="fas fa-check"></i> Doctors, nurses, lab techs, pharmacists</li>
                    <li><i class="fas fa-check"></i> Full-time, part-time, locum, or shift cover</li>
                    <li><i class="fas fa-check"></i> Contracts &amp; payments managed in one place</li>
                </ul>
                <button class="btn btn-primary" data-open-modal="facilityModal">Start facility hire</button>
            </article>

            <article class="hire-card" id="care">
                <i class="fas fa-user-nurse"></i>
                <h2>Personal home care</h2>
                <p>Trained nurses and caregivers for elderly support, post-op recovery, or chronic care at home.</p>
                <ul class="check-list">
                    <li><i class="fas fa-check"></i> Vetted, licensed caregivers</li>
                    <li><i class="fas fa-check"></i> Single visits, weekly schedules, or 24/7 cover</li>
                    <li><i class="fas fa-check"></i> Care plans tailored to your needs</li>
                </ul>
                <button class="btn btn-primary" data-open-modal="careModal">Find personal care</button>
            </article>
        </div>
    </div>
</section>

<section class="section section-alt" id="how-it-works">
    <div class="container">
        <h2 class="section-title">How Matendo works</h2>
        <ol class="steps">
            <li><span class="step-num">1</span><h3>Submit request</h3><p>Tell us your role, location and start date.</p></li>
            <li><span class="step-num">2</span><h3>Review &amp; match</h3><p>Our team curates a shortlist within 24 hours.</p></li>
            <li><span class="step-num">3</span><h3>Connect</h3><p>Interview your matches via secure messaging or video.</p></li>
            <li><span class="step-num">4</span><h3>Start working</h3><p>Sign, schedule and pay through Matendo's escrow.</p></li>
        </ol>
    </div>
</section>

<!-- =================== FACILITY MODAL ==================== -->
<div class="modal" id="facilityModal" hidden role="dialog" aria-modal="true" aria-labelledby="facilityTitle">
    <div class="modal-card">
        <header>
            <h3 id="facilityTitle">Start facility hiring</h3>
            <button class="modal-close" aria-label="Close" data-close-modal>&times;</button>
        </header>
        <form id="facilityForm" method="POST" enctype="multipart/form-data" novalidate
              data-endpoint="<?= e(url('api/facility-request.php')) ?>">
            <?= csrf_field() ?>

            <fieldset>
                <legend>Facility details</legend>
                <div class="grid-2">
                    <div class="field"><label>Facility name *</label>
                        <input type="text" name="facilityName" required minlength="3" maxlength="190"></div>
                    <div class="field"><label>Contact person *</label>
                        <input type="text" name="contactPerson" required pattern="[A-Za-z\' \-]{3,}"></div>
                    <div class="field"><label>Email *</label>
                        <input type="email" name="email" required></div>
                    <div class="field"><label>Phone *</label>
                        <input type="tel" name="phone" required pattern="[0-9+\-\s()]{7,20}"></div>
                </div>
                <input type="hidden" name="coordinates" id="facilityCoords">
            </fieldset>

            <fieldset>
                <legend>Facility type</legend>
                <div class="checkboxes">
                    <?php foreach (['Hospital','Clinic','Laboratory','Pharmacy','Other'] as $t): ?>
                        <label><input type="checkbox" name="facilityType[]" value="<?= e($t) ?>"> <?= e($t) ?></label>
                    <?php endforeach; ?>
                </div>
                <div class="field"><label>If other, please specify</label>
                    <input type="text" name="otherFacilityType" maxlength="190"></div>
            </fieldset>

            <fieldset>
                <legend>Positions needed</legend>
                <div class="checkboxes">
                    <?php foreach (['Medical Officers','Nurses/Midwives','Lab Technologists','Radiographers','Pharmacy Dispensers','Pharmacists','Other'] as $p): ?>
                        <label><input type="checkbox" name="positions[]" value="<?= e($p) ?>"> <?= e($p) ?></label>
                    <?php endforeach; ?>
                </div>
                <div class="field"><label>If other, please specify</label>
                    <input type="text" name="otherPosition" maxlength="190"></div>
            </fieldset>

            <fieldset>
                <legend>Engagement</legend>
                <div class="grid-2">
                    <div>
                        <label>Duration</label>
                        <div class="checkboxes">
                            <?php foreach (['Short-Term','Part-Time','Full-Time'] as $d): ?>
                                <label><input type="checkbox" name="duration[]" value="<?= e($d) ?>"> <?= e($d) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label>Shift type</label>
                        <div class="checkboxes">
                            <?php foreach (['Day Shift','Night Shift','Rotating'] as $s): ?>
                                <label><input type="checkbox" name="shiftType[]" value="<?= e($s) ?>"> <?= e($s) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="field"><label>Number of staff *</label>
                        <input type="number" name="staffNumber" min="1" max="1000" required></div>
                    <div class="field"><label>Start date *</label>
                        <input type="date" name="startDate" min="<?= e($today) ?>" required></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Job requirements</legend>
                <div class="radio-row">
                    <label><input type="radio" name="job-requirement-option" value="none" checked> Skip for now</label>
                    <label><input type="radio" name="job-requirement-option" value="upload"> Upload JD (PDF/DOC)</label>
                    <label><input type="radio" name="job-requirement-option" value="manual"> Type details</label>
                </div>

                <div data-show-when="job-requirement-option=upload">
                    <div class="field"><label>Job description file (PDF, DOC, DOCX â€” max 10 MB)</label>
                        <input type="file" name="jobDescriptionFile" accept=".pdf,.doc,.docx"></div>
                </div>
                <div data-show-when="job-requirement-option=manual">
                    <div class="field"><label>Required qualifications</label>
                        <textarea name="qualifications" rows="3" maxlength="2000"></textarea></div>
                    <div class="field"><label>Experience required</label>
                        <textarea name="experience" rows="3" maxlength="2000"></textarea></div>
                    <div class="field"><label>Job description / responsibilities</label>
                        <textarea name="jobDescription" rows="4" maxlength="5000"></textarea></div>
                </div>
            </fieldset>

            <label class="checkbox-confirm">
                <input type="checkbox" required>
                <span>I confirm the information above is accurate and consent to Matendo processing it to fulfil my request.</span>
            </label>

            <div class="form-feedback" data-feedback hidden></div>
            <div class="form-actions">
                <button type="button" class="btn btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="btn btn-primary">Submit request</button>
            </div>
        </form>
    </div>
</div>

<!-- =================== CARE MODAL ==================== -->
<div class="modal" id="careModal" hidden role="dialog" aria-modal="true" aria-labelledby="careTitle">
    <div class="modal-card">
        <header>
            <h3 id="careTitle">Find personal care</h3>
            <button class="modal-close" aria-label="Close" data-close-modal>&times;</button>
        </header>
        <form id="careForm" novalidate data-endpoint="<?= e(url('api/care-request.php')) ?>">
            <?= csrf_field() ?>

            <fieldset>
                <legend>Patient details</legend>
                <div class="grid-2">
                    <div class="field"><label>Full name *</label>
                        <input type="text" name="fullName" required minlength="3" pattern="[A-Za-z\' \-]{3,}"></div>
                    <div class="field"><label>Email *</label>
                        <input type="email" name="email" required></div>
                    <div class="field"><label>Phone *</label>
                        <input type="tel" name="phone" required pattern="[0-9+\-\s()]{7,20}"></div>
                    <div class="field"><label>Address</label>
                        <input type="text" name="address" maxlength="255"></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Care needs</legend>
                <div class="field"><label>Type of care *</label>
                    <select name="careType" required>
                        <option value="">Selectâ€¦</option>
                        <option>Elderly care</option>
                        <option>Post-surgery recovery</option>
                        <option>Chronic condition support</option>
                        <option>Physical therapy</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="field"><label>If other, specify</label>
                    <input type="text" name="otherCareType" maxlength="120"></div>
                <div class="field"><label>Care requirements *</label>
                    <textarea name="careRequirements" rows="3" required minlength="10" maxlength="2000"></textarea></div>
                <label>Schedule</label>
                <div class="checkboxes">
                    <?php foreach (['Full-Time','Part-Time','Occasional'] as $s): ?>
                        <label><input type="checkbox" name="schedule[]" value="<?= e($s) ?>"> <?= e($s) ?></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Medical information <span class="muted small">(encrypted at rest)</span></legend>
                <div class="field"><label>Medical conditions</label>
                    <textarea name="medicalConditions" rows="2" maxlength="2000"></textarea></div>
                <div class="field"><label>Medications</label>
                    <textarea name="medications" rows="2" maxlength="2000"></textarea></div>
                <div class="field"><label>Allergies</label>
                    <textarea name="allergies" rows="2" maxlength="2000"></textarea></div>
            </fieldset>

            <fieldset>
                <legend>Emergency contact</legend>
                <div class="grid-2">
                    <div class="field"><label>Name *</label>
                        <input type="text" name="emergencyContact" required maxlength="190"></div>
                    <div class="field"><label>Phone *</label>
                        <input type="tel" name="emergencyPhone" required pattern="[0-9+\-\s()]{7,20}"></div>
                </div>
            </fieldset>

            <label class="checkbox-confirm">
                <input type="checkbox" required>
                <span>I consent to Matendo securely storing and processing this information to coordinate care.</span>
            </label>

            <div class="form-feedback" data-feedback hidden></div>
            <div class="form-actions">
                <button type="button" class="btn btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="btn btn-primary">Submit request</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= e(asset('assets/js/forms.js')) ?>" defer></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
