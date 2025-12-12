export function updateClock() {
    const now = new Date();
    const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false
    };
    const formattedDate = now.toLocaleDateString('fr-FR', options);
    document.getElementById('clock').textContent = formattedDate;
}

updateClock();
setInterval(updateClock, 1000);
