<?php
// On ne bloque rien côté PHP, juste HTML si besoin
?>

<div id="cookie-banner" class="cookie-banner hidden">
    <p>Nous utilisons des cookies pour améliorer votre expérience sur ce site. 
       Vous pouvez accepter, personnaliser ou refuser certains cookies.</p>
    <div class="cookie-actions">
        <button id="accept-cookies">Accepter tous</button>
        <button id="custom-cookies">Personnaliser</button>
        <button id="reject-cookies">Refuser</button>
    </div>
</div>

<div id="cookie-settings" class="cookie-settings hidden">
    <h2>Préférences cookies</h2>
    <form id="cookie-form">
        <label>
            <input type="checkbox" name="essential" checked disabled>
            Cookies essentiels
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
        <p class="cookie-warning">⚠️ Certaines fonctionnalités peuvent être limitées si vous refusez des cookies.</p>
    </form>
</div>
