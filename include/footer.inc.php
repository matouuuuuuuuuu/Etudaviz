<?php
    require_once(__DIR__ . '/functions.inc.php');
    $visites = incrementCounter();
    $today = getCurrentDate();
?>
</main>

<footer>
    <div class="footer-container">
        <div class="footer-links">
            <a href="/mentions-legales.php">Mentions légales</a>
            <a href="/confidentialite.php">Politique de confidentialité</a>
            <a href="/contact.php">Nous contacter</a>
        </div>

        <div class="footer-copy">
            <span>© 2025 Projet Etudaviz. Tous droits réservés.</span>
        </div>


        <!-- Infos -->
        <div class="footer-infos">
            <span>Nombre de visites : <?= $visites ?></span>
            <span><?= date("d/m/Y"); ?></span>
        </div>

        <!-- Icônes réseaux -->
        <div class="footer-socials">
            <a href="https://facebook.com" target="_blank" aria-label="Facebook">
                <img class="social" src="/images/facebookicon.png" alt="Logo Facebook"/>
            </a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                <img class="social" src="/images/instaicon.png" alt="Logo Instagram"/>
            </a>
            
        </div>
    </div>
</footer>
<script src="/js/theme.js"></script>
<!-- Bandeau cookies -->
    <div id="cookie-banner" class="cookie-banner" aria-live="polite">
        <div class="cookie-content">
            <p class="cookie-text">
                Ce site utilise des cookies pour améliorer votre expérience.
            </p>
            <div class="cookie-actions">
                <button id="cookie-accept" class="cookie-btn cookie-btn-primary">
                    Accepter
                </button>
                <button id="cookie-refuse" class="cookie-btn cookie-btn-secondary">
                    Refuser
                </button>
            </div>
        </div>
    </div>

<script src="js/cookies.js"></script>
</body>
</html>
