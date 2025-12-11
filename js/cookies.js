// js/cookies.js

const COOKIE_KEY = "etudaviz_cookie_consent";   // "accepted" | "refused"
const PREF_KEY_PREFIX = "etudaviz_pref_";

function getCookieConsent() {
    try {
        return localStorage.getItem(COOKIE_KEY);
    } catch (e) {
        return null;
    }
}

function setCookieConsent(value) {
    try {
        localStorage.setItem(COOKIE_KEY, value);
    } catch (e) {}
}

function showCookieBanner() {
    const banner = document.getElementById("cookie-banner");
    if (!banner) return;
    banner.style.display = "flex";
}

function hideCookieBanner() {
    const banner = document.getElementById("cookie-banner");
    if (!banner) return;
    banner.style.display = "none";
}

// Helper pour sauver des préférences (thème, etc.) UNIQUEMENT si consentement
function saveUserPreference(key, value) {
    if (getCookieConsent() !== "accepted") return;
    try {
        localStorage.setItem(PREF_KEY_PREFIX + key, value);
    } catch (e) {}
}

function loadUserPreference(key) {
    try {
        return localStorage.getItem(PREF_KEY_PREFIX + key);
    } catch (e) {
        return null;
    }
}

// Initialisation
document.addEventListener("DOMContentLoaded", function () {
    const consent = getCookieConsent();
    const btnAccept = document.getElementById("cookie-accept");
    const btnRefuse = document.getElementById("cookie-refuse");

    if (!consent) {
        // aucun choix fait -> on affiche le bandeau
        showCookieBanner();
    }

    if (btnAccept) {
        btnAccept.addEventListener("click", function () {
            setCookieConsent("accepted");
            hideCookieBanner();
            // Ici tu peux initialiser proprement d’autres prefs si besoin
        });
    }

    if (btnRefuse) {
        btnRefuse.addEventListener("click", function () {
            setCookieConsent("refused");
            hideCookieBanner();
            // Ici, pas de sauvegarde de préférences supplémentaires
        });
    }

    // Expose les helpers globalement si tu en as besoin ailleurs
    window.etudavizCookie = {
        getConsent: getCookieConsent,
        savePref: saveUserPreference,
        loadPref: loadUserPreference
    };
});
