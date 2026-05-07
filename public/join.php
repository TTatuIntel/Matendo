<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pageTitle = 'Join as a medical professional â€” Matendo Medics';
$pageDescription = 'Apply to join the Matendo network. Doctors, nurses, lab techs, pharmacists and caregivers. Vetted, verified, in demand.';
$activeNav = 'join';
include __DIR__ . '/../includes/header.php';

$today = date('Y-m-d');
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-text" data-reveal="left">
            <span class="eyebrow">Join the network</span>
            <h1>Apply in three quick steps</h1>
            <p class="lede">Tell us who you are, upload your documents, and share your preferences. Screening typically takes 3â€“5 business days.</p>
        </div>
        <div class="hero-art" data-reveal="right" aria-hidden="true">
            <img src="<?= e(asset('images/nurse1.png')) ?>" alt="">
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="wizard">
            <div class="wizard-header">
                <h2>Application</h2>
                <p>Fields marked * are required. Documents are stored privately and never shared without your consent.</p>
            </div>

            <ol class="stepper" id="joinStepper" aria-label="Application progress">
                <li class="stepper-item is-active" data-step="1"><span>About you</span></li>
                <li class="stepper-item"           data-step="2"><span>Documents</span></li>
                <li class="stepper-item"           data-step="3"><span>Preferences</span></li>
            </ol>

            <form id="joinForm" method="POST" enctype="multipart/form-data" novalidate
                  data-endpoint="<?= e(url('api/join-application.php')) ?>">
                <?= csrf_field() ?>

                <!-- ===== STEP 1 ===== -->
                <div class="form-step is-active" data-step="1">
                    <fieldset>
                        <legend>Personal details</legend>
                        <div class="grid-2">
                            <div class="field"><label>First name *</label>
                                <input type="text" name="firstName" required pattern="[A-Za-z\' \-]{2,}"></div>
                            <div class="field"><label>Last name *</label>
                                <input type="text" name="lastName" required pattern="[A-Za-z\' \-]{2,}"></div>
                            <div class="field"><label>Email *</label>
                                <input type="email" name="email" required></div>
                            <div class="field"><label>Phone *</label>
                                <input type="tel" name="phone" required pattern="[0-9+\-\s()]{7,20}"></div>
                            <div class="field span-2"><label>Address</label>
                                <input type="text" name="address" maxlength="255"></div>
                            <div class="field"><label>City / town</label>
                                <input type="text" name="location" maxlength="190"></div>
                            <input type="hidden" name="coordinates" id="joinCoords">
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Profession</legend>
                        <div class="grid-2">
                            <div class="field"><label>Profession *</label>
                                <select name="profession" required>
                                    <option value="">Selectâ€¦</option>
                                    <option>Doctor</option>
                                    <option>Nurse / Midwife</option>
                                    <option>Pharmacist</option>
                                    <option>Lab Technologist</option>
                                    <option>Radiographer</option>
                                    <option>Caregiver</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="field"><label>If other, specify</label>
                                <input type="text" name="otherProfession" maxlength="120"></div>
                            <div class="field"><label>Specialisation</label>
                                <input type="text" name="specialization" maxlength="190"></div>
                            <div class="field"><label>Years of experience *</label>
                                <input type="number" name="yearsExperience" min="0" max="60" required></div>
                            <div class="field span-2"><label>License number</label>
                                <input type="text" name="licenseNumber" maxlength="120"></div>
                        </div>
                    </fieldset>
                </div>

                <!-- ===== STEP 2 ===== -->
                <div class="form-step" data-step="2">
                    <fieldset>
                        <legend>Documents <span class="muted small">(PDF, JPG, PNG â€” max 10 MB each)</span></legend>
                        <div class="grid-2">
                            <div class="field"><label>Resume / CV *</label>
                                <input type="file" name="resume" accept=".pdf,.doc,.docx" required></div>
                            <div class="field"><label>License document</label>
                                <input type="file" name="license" accept=".pdf,.jpg,.jpeg,.png"></div>
                            <div class="field span-2"><label>Certifications</label>
                                <input type="file" name="certifications" accept=".pdf,.jpg,.jpeg,.png"></div>
                        </div>
                    </fieldset>
                </div>

                <!-- ===== STEP 3 ===== -->
                <div class="form-step" data-step="3">
                    <fieldset>
                        <legend>Work preferences</legend>
                        <div class="grid-2">
                            <div>
                                <label>Work type</label>
                                <div class="checkboxes">
                                    <?php foreach (['Full-Time','Part-Time','Short-Term'] as $w): ?>
                                        <label><input type="checkbox" name="workType[]" value="<?= e($w) ?>"> <?= e($w) ?></label>
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
                            <div class="field"><label>Preferred location</label>
                                <input type="text" name="preferredLocation" maxlength="190"></div>
                            <div class="field"><label>Available from *</label>
                                <input type="date" name="startDate" min="<?= e($today) ?>" required></div>
                        </div>
                    </fieldset>

                    <label class="checkbox-confirm">
                        <input type="checkbox" required>
                        <span>I confirm the information provided is accurate and consent to background and license verification.</span>
                    </label>
                </div>

                <div class="form-feedback" data-feedback hidden></div>

                <div class="wizard-actions">
                    <button type="button" class="btn btn-ghost" id="joinBack" hidden>&larr; Back</button>
                    <span class="spacer"></span>
                    <button type="button" class="btn btn-primary" id="joinNext">Next &rarr;</button>
                    <button type="submit" class="btn btn-primary" id="joinSubmit" hidden>Submit application</button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">What happens next</h2>
        <ol class="steps">
            <li><span class="step-num">1</span><h3>Application review</h3><p>We confirm receipt within hours and begin verification.</p></li>
            <li><span class="step-num">2</span><h3>Credentials &amp; interview</h3><p>License check, document review and a short clinical interview.</p></li>
            <li><span class="step-num">3</span><h3>Profile activation</h3><p>Approved professionals get a public profile and can accept matches.</p></li>
            <li><span class="step-num">4</span><h3>Start working</h3><p>Receive matched opportunities, sign through the platform, get paid weekly.</p></li>
        </ol>
    </div>
</section>

<script src="<?= e(asset('assets/js/forms.js')) ?>" defer></script>
<script>
(() => {
    const form    = document.getElementById('joinForm');
    if (!form) return;
    const steps   = [...form.querySelectorAll('.form-step')];
    const dots    = [...document.querySelectorAll('#joinStepper .stepper-item')];
    const btnBack = document.getElementById('joinBack');
    const btnNext = document.getElementById('joinNext');
    const btnSub  = document.getElementById('joinSubmit');
    let current = 1;
    const total = steps.length;

    const render = () => {
        steps.forEach(s => s.classList.toggle('is-active', +s.dataset.step === current));
        dots.forEach(d => {
            const n = +d.dataset.step;
            d.classList.toggle('is-active', n === current);
            d.classList.toggle('is-done',   n <  current);
        });
        btnBack.hidden = current === 1;
        btnNext.hidden = current === total;
        btnSub.hidden  = current !== total;
    };

    // Validate only the visible step's required fields before advancing.
    const validateStep = () => {
        const panel = steps.find(s => +s.dataset.step === current);
        const fields = panel.querySelectorAll('input, select, textarea');
        for (const f of fields) {
            if (!f.checkValidity()) { f.reportValidity(); return false; }
        }
        return true;
    };

    btnNext.addEventListener('click', () => {
        if (!validateStep()) return;
        if (current < total) { current++; render(); window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' }); }
    });
    btnBack.addEventListener('click', () => {
        if (current > 1) { current--; render(); }
    });
    render();
})();
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
