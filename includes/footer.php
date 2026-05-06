</main>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col">
            <img src="<?= e(asset('images/matendo-logo.svg')) ?>" alt="Matendo Medics" class="footer-logo">
            <p>Connecting healthcare facilities, patients and professionals across East Africa.</p>
            <div class="social-icons" aria-label="Social media">
                <a href="#" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                <a href="#" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                <a href="#" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin" aria-hidden="true"></i></a>
                <a href="#" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>Company</h3>
            <ul>
                <li><a href="<?= e(url('about.php'))   ?>">About</a></li>
                <li><a href="<?= e(url('careers.php')) ?>">Careers</a></li>
                <li><a href="<?= e(url('contact.php')) ?>">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Services</h3>
            <ul>
                <li><a href="<?= e(url('hire.php'))   ?>">Hire staff</a></li>
                <li><a href="<?= e(url('talent.php')) ?>">Find talent</a></li>
                <li><a href="<?= e(url('join.php'))   ?>">Join as professional</a></li>
                <li><a href="<?= e(url('hire.php#personal-care')) ?>">Personal home care</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Trust &amp; legal</h3>
            <ul>
                <li><a href="<?= e(url('legal/privacy.php')) ?>">Privacy Policy</a></li>
                <li><a href="<?= e(url('legal/terms.php'))   ?>">Terms of Service</a></li>
                <li><a href="<?= e(url('legal/security.php'))?>">Security &amp; HIPAA</a></li>
                <li><a href="<?= e(url('contact.php')) ?>">Help center</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Contact</h3>
            <ul class="contact-list">
                <li><i class="fas fa-envelope"></i> <a href="mailto:info@matendo.com">info@matendo.com</a></li>
                <li><i class="fas fa-phone"></i> <a href="tel:+256781053105">+256 781 053 105</a></li>
                <li><i class="fas fa-map-marker-alt"></i> Kampala, Uganda</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <small>&copy; <?= date('Y') ?> Matendo Medics. All rights reserved.</small>
    </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Back to top" hidden>
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
</button>

<div class="cookie-consent" id="cookieConsent" hidden>
    <p>We use essential cookies to operate this site. With your consent, we also use analytics cookies to improve it.</p>
    <div class="cookie-buttons">
        <button class="btn btn-primary" id="acceptCookies">Accept</button>
        <button class="btn btn-outline" id="declineCookies">Decline</button>
    </div>
</div>

<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
