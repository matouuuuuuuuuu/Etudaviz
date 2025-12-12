<?php
require "./include/functions.inc.php";

// --- Recherche ESCO ---
$query = $_GET["q"] ?? "";
$limit = 4;
$searchResults = [];
$isRandom = false;

// --- MÉTIERS ALÉATOIRES PAR DÉFAUT ---
$randomPool = [
    "ingénieur",
    "médecin",
    "boulanger",
    "développeur",
    "infirmier",
    "architecte",
    "enseignant",
    "graphiste",
    "mécanicien",
    "cuisinier"
];

if ($query === "") {
    // MODE : métiers aléatoires
    $isRandom = true;

    shuffle($randomPool);
    $randomSet = array_slice($randomPool, 0, 4);

    $searchResults = [];
    foreach ($randomSet as $word) {
        $result = escoSearch($word, 1, 0);
        if (!empty($result)) {
            $searchResults[] = $result[0];
        }
    }

    // Barre de recherche vide
    $query = "";
} else {
    // MODE : recherche classique
    $searchResults = escoSearch($query, $limit, 0);
}

// --- Meta ---
$title = "Métiers";
$description = "Découvrez les métiers par domaine ou via une recherche";
$h1 = "Découvrir des métiers";
$canonical = "https://etudaviz.alwaysdata.net/metiers.php";

require "./include/header.inc.php";
?>

<section class="metiers-container">

    <!-- Barre de recherche -->
    <form action="metiers.php" method="get" class="search-bar">
        <label for="searchInput" class=".visually-hidden">Rechercher un métier</label>
        <input type="text" id="searchInput" name="q" placeholder="Rechercher un métier..." value="<?= htmlspecialchars($query) ?>">
        <button type="submit">🔍</button>
    </form>

</section>

<?php if ($isRandom): ?>

    <!-- TITRE ALÉATOIRE -->
    <h2 class="result-title">✨ Métiers aléatoires</h2>

    <div class="liste-metiers" id="metier-list">
        <?php foreach ($searchResults as $m): ?>
            <?= renderMetierCard($m) ?>
        <?php endforeach; ?>
    </div>

<?php else: ?>

    <!-- TITRE RECHERCHE -->
    <h2 class="result-title">
        Résultats pour : <strong><?= htmlspecialchars($query) ?></strong>
    </h2>

    <div class="liste-metiers" id="metier-list">
        <?php if (empty($searchResults)): ?>
            <p class="no-result">Aucun métier trouvé.</p>
        <?php else: ?>
            <?php foreach ($searchResults as $m): ?>
                <?= renderMetierCard($m) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (count($searchResults) === $limit): ?>
        <div class="results">
            <button id="voir-plus"
                    data-page="2"
                    data-query="<?= htmlspecialchars($query) ?>">
                Voir plus
            </button>
        </div>
    <?php endif; ?>

<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btn  = document.getElementById("voir-plus");
    const list = document.getElementById("metier-list");

    if (!btn || !list) return; // Pas de bouton = pas de JS ← normal en mode aléatoire

    btn.addEventListener("click", () => {
        const page  = parseInt(btn.dataset.page) || 2;
        const query = btn.dataset.query || '';

        btn.disabled = true;
        btn.textContent = "Chargement...";

        const url = "load-metiers.php?q=" + encodeURIComponent(query) + "&page=" + page;

        fetch(url)
            .then(r => r.text())
            .then(html => {
                const trimmed = html.trim();

                if (!trimmed) {
                    btn.style.display = "none";
                    return;
                }

                list.insertAdjacentHTML("beforeend", trimmed);

                btn.dataset.page = page + 1;
                btn.disabled = false;
                btn.textContent = "Voir plus";
            })
            .catch(err => {
                console.error("Erreur de chargement des métiers :", err);
                btn.disabled = false;
                btn.textContent = "Erreur";
            });
    });
});
</script>

<?php require "./include/footer.inc.php"; ?>
