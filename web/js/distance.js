// ./js/distance.js
async function calculateDistance() {
  const city = document.getElementById("userAddress").value.trim();
  const resultContainer = document.getElementById("distanceResult");

  if (!city) {
    resultContainer.textContent = "Veuillez entrer une ville ou une adresse.";
    return;
  }

  const destLat = window.mapLat;
  const destLon = window.mapLon;
  const apiKey = "2bb409fc044b442194d547bfa30e7862";
  resultContainer.textContent = "Calcul en cours...";

  try {
    // --- 1️⃣ Géocoder la ville/adresse saisie ---
    const geoUrl = `https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(
      city + ", France"
    )}&lang=fr&apiKey=${apiKey}`;
    const geoRes = await fetch(geoUrl);
    const geoData = await geoRes.json();

    if (!geoData.features || geoData.features.length === 0) {
      resultContainer.textContent = "Ville introuvable.";
      return;
    }

    const userLat = geoData.features[0].geometry.coordinates[1];
    const userLon = geoData.features[0].geometry.coordinates[0];
    const region = geoData.features[0].properties.state || "";
    const cityName = geoData.features[0].properties.city || city;

    // --- 2️⃣ Calculer les trajets : voiture + transport en commun ---
    const driveUrl = `https://api.geoapify.com/v1/routing?waypoints=${userLat},${userLon}|${destLat},${destLon}&mode=drive&lang=fr&details=instruction_details&apiKey=${apiKey}`;
    const transitUrl = `https://api.geoapify.com/v1/routing?waypoints=${userLat},${userLon}|${destLat},${destLon}&mode=transit&lang=fr&apiKey=${apiKey}`;

    const [driveRes, transitRes] = await Promise.all([
      fetch(driveUrl).then((r) => r.json()),
      fetch(transitUrl).then((r) => r.json()),
    ]);

    if (!driveRes.features || driveRes.features.length === 0) {
      resultContainer.textContent = "Itinéraire introuvable.";
      return;
    }

    const driveProps = driveRes.features[0].properties;
  const transitProps = transitRes.features?.[0]?.properties || transitRes.features?.[0]?.features?.[0]?.properties || null;

    // --- 3️⃣ Formater les données ---
    const distanceKm = (driveProps.distance / 1000).toFixed(1);

    const driveDurationTxt = formatDuration(driveProps.time);
    const transitDurationTxt = transitProps
      ? formatDuration(transitProps.time)
      : "Non disponible";

    // --- 4️⃣ Affichage propre ---
    resultContainer.innerHTML = `
      <div class="distance-info">
        🚗 <strong>Distance :</strong> ${distanceKm} km – <strong>En voiture :</strong> ${driveDurationTxt}<br>
        🚌 <strong>En transports en commun :</strong> ${transitDurationTxt}<br>
        📍 <small>Depuis ${cityName}${region ? ` (${region})` : ""}</small>
      </div>
    `;
  } catch (err) {
    console.error(err);
    resultContainer.textContent = "Erreur lors du calcul.";
  }
}

// --- Fonction utilitaire pour formater la durée ---
function formatDuration(seconds) {
  const h = Math.floor(seconds / 3600);
  const m = Math.round((seconds % 3600) / 60);
  return `${h > 0 ? h + "h " : ""}${m}min`;
}
