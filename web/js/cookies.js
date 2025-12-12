function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Lax`;
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let c of ca) {
        c = c.trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
}

document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookie-banner');
    const settings = document.getElementById('cookie-settings');
    const consent = getCookie('cookie_consent');

    // Affiche la bannière seulement si cookie inexistant
    if (!consent && banner) banner.classList.remove('hidden');

    // Accepter tous
    document.getElementById('accept-cookies')?.addEventListener('click', () => {
        setCookie('cookie_consent', 'all', 365); // long terme
        if (banner) banner.classList.add('hidden');
    });

    // Personnaliser
    document.getElementById('custom-cookies')?.addEventListener('click', () => {
        if (banner) banner.classList.add('hidden');
        if (settings) settings.classList.remove('hidden');
    });

    // Refuser
    document.getElementById('reject-cookies')?.addEventListener('click', () => {
        setCookie('cookie_consent', 'refused', 1); // court terme
        if (banner) banner.classList.add('hidden');
    });

    // Enregistrement choix personnalisé
    document.getElementById('cookie-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const form = e.target;
        const preferences = {
            essential: true,
            analytics: form.analytics.checked,
            marketing: form.marketing.checked
        };
        setCookie('cookie_consent', JSON.stringify(preferences), 365);
        if (settings) settings.classList.add('hidden');
    });

    // ---------- NOUVEAU : gestion du bouton "Gérer mes préférences cookies" ----------
    const editBtn = document.getElementById('edit-cookies-btn');
    const popup = document.getElementById('cookie-settings-popup');
    const closePopup = document.getElementById('close-cookie-popup');

    if (editBtn && popup) {
        editBtn.addEventListener('click', (e) => {
            e.preventDefault(); // empêche le scroll en haut
            popup.classList.remove('hidden');
        });
    }

    if (closePopup && popup) {
        closePopup.addEventListener('click', () => {
            popup.classList.add('hidden');
        });
    }
});
