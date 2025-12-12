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
            <a id="edit-cookies-btn" href="#" role="button">Gérer mes préférences cookies</a>        </div>
        <div id="cookie-settings-popup" class="cookie-settings hidden" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background:#1a1a1a; color:#ccc; padding:20px; border-radius:10px; z-index:10000;">
            <h2>Préférences cookies</h2>
            <form id="cookie-form-popup">
                <label>
                    <input type="checkbox" name="essential" checked disabled>
                    Cookies essentiels (nécessaires)
                </label><br>
                <label>
                    <input type="checkbox" name="analytics">
                    Cookies d’analyse
                </label><br>
                <label>
                    <input type="checkbox" name="marketing">
                    Cookies marketing
                </label><br>
                <button type="submit">Enregistrer mes choix</button>
                <button type="button" id="close-cookie-popup">Fermer</button>
                <p style="margin-top:10px; color:#f90;">⚠️ Refuser certains cookies peut limiter certaines fonctionnalités.</p>
            </form>
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
<a href="#" class="fleche back-to-top" aria-label="Retour en haut">
  <img src="/images/fleche.png" alt="Retour en haut">
</a>

<script src="/js/theme.js"></script>
<script src="/js/back-to-top.js"></script>
<script src="/js/cookies.js"></script>
<script src="/js/popup.js"></script>

</body>
</html>

