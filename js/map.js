// ./js/map.js
document.addEventListener("DOMContentLoaded", () => {
  if (!window.L) return;
  const mapEl = document.getElementById("map");
  if (!mapEl) return;

  const lat = Number.isFinite(window.mapLat) ? window.mapLat : 46.603354;
  const lon = Number.isFinite(window.mapLon) ? window.mapLon : 1.888334;
  const zoom = Number.isFinite(window.mapZoom) ? window.mapZoom : 6;

  const map = L.map("map").setView([lat, lon], zoom);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  // Si un marqueur unique est passé
  if (Number.isFinite(window.mapLat) && Number.isFinite(window.mapLon)) {
    const label = window.mapMarkerLabel || "Établissement";
    L.marker([window.mapLat, window.mapLon]).addTo(map).bindPopup(label).openPopup();
  }

  // ⚡️ Ajout de plusieurs markers depuis PHP
  if (Array.isArray(window.mapMarkers)) {
    window.mapMarkers.forEach(etab => {
      if (etab.latitude && etab.longitude) {
        L.marker([etab.latitude, etab.longitude])
         .addTo(map)
         .bindPopup(`<b>${etab.nom}</b><br>${etab.ville}<br>${etab.type || ''}`);
      }
    });
  }
});
