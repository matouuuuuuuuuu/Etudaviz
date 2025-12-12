document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('edit-cookies-btn');
    const popup = document.getElementById('cookie-settings-popup');
    const form = document.getElementById('cookie-form-popup');
    const closeBtn = document.getElementById('close-cookie-popup');

    editBtn?.addEventListener('click', () => {
        popup.classList.remove('hidden');
        const consent = JSON.parse(getCookie('cookie_consent') || '{"essential":true,"analytics":false,"marketing":false}');
        form.analytics.checked = consent.analytics;
        form.marketing.checked = consent.marketing;
    });

    closeBtn?.addEventListener('click', () => {
        popup.classList.add('hidden');
    });

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const preferences = {
            essential: true,
            analytics: form.analytics.checked,
            marketing: form.marketing.checked
        };
        setCookie('cookie_consent', JSON.stringify(preferences), 365);
        popup.classList.add('hidden');
        alert('Vos préférences ont été mises à jour.');
    });
});
