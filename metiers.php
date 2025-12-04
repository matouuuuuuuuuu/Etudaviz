<?php
require "./include/functions.inc.php";

// --- Recherche ESCO ---
$query = $_GET["q"] ?? "";
$searchResults = $query ? escoSearch($query) : [];

// --- Meta ---
$title = "Métiers";
$description = "Découvrez les métiers par domaine ou via une recherche";
$h1 = "Découvrir des métiers";
$canonical = "https://etudaviz.alwaysdata.net/metiers.php";

require "./include/header.inc.php";

// --- Catégories ---
$categories = [
    ["id" => "science", "label" => "Sciences", "emoji" => "🔬", "color" => "#2ecc71"],
    ["id" => "technologie", "label" => "Technologie", "emoji" => "🧪", "color" => "#9b59b6"],
    ["id" => "art", "label" => "Arts & Création", "emoji" => "🎨", "color" => "#e74c3c"],
    ["id" => "droit", "label" => "Droit & Justice", "emoji" => "⚖️", "color" => "#3498db"],
    ["id" => "sante", "label" => "Santé", "emoji" => "🩺", "color" => "#e67e22"]
];
?>

<section>

    <!-- 🔎 Barre de recherche identique à formations.php -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="q" 
                   placeholder="Rechercher un métier..."
                   value="<?= htmlspecialchars($query) ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>


    <?php if (!$query): ?>
    
        <!-- 🎨 Affichage des catégories tant qu'il n'y a PAS de recherche -->
        <div class="metier-categories">
            <?php foreach ($categories as $cat): ?>
                <a href="metiers-domaine.php?cat=<?= $cat['id'] ?>"
                class="categorie-card"
                style="
                    --color1: <?= $cat['color'] ?>20;
                    --color2: <?= $cat['color'] ?>05;
                    --bordercolor: <?= $cat['color'] ?>;
                ">
                    <div class="emoji"><?= $cat['emoji'] ?></div>
                    <span><?= $cat['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <!-- 🧠 Résultats affichés au même format que les formations -->
        <h2 class="result-title">
            Résultats pour : <strong><?= htmlspecialchars($query) ?></strong>
        </h2>

        <?php if (empty($searchResults)): ?>
            <p class="no-result">Aucun métier trouvé.</p>

        <?php else: ?>
            <ul id="metier-list">
                <?php foreach ($searchResults as $m): ?>
                    <?= renderMetierCard($m) ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>

</section>

<?php require "./include/footer.inc.php"; ?>
