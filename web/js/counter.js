/**
 * counter.js
 * Animation de compteurs numériques progressifs
 * pour les statistiques de la section "Notre impact en chiffres"
 */

document.addEventListener('DOMContentLoaded', () => {
  const counters = document.querySelectorAll('.stat-item');

  counters.forEach(el => {
    const match = el.textContent.match(/\d+/);
    if (!match) return;

    const target = parseInt(match[0]);
    let count = 0;
    const speed = 25; // vitesse de rafraîchissement (ms)
    const steps = 50; // nombre d'incréments jusqu'à la valeur finale
    const increment = target / steps;

    const interval = setInterval(() => {
      count += increment;
      if (count >= target) {
        count = target;
        clearInterval(interval);
      }
      el.textContent = el.textContent.replace(/\d+/, Math.round(count));
    }, speed);
  });
});
