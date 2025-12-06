<?php
require "./include/functions.inc.php";

$title="Recherche sur Etudaviz";
$description="Formations + métiers correspondant à votre recherche.";
$h1="Résultats pour votre recherche";
$canonical="https://etudaviz.alwaysdata.net/recherche.php";

$query = isset($_GET['q']) ? trim($_GET['q']) : "";

require "./include/header.inc.php";

// Résultats FORMATIONS
$formations = [];
if ($query !== "") {
    $rawResults = getEtablissementsSupPublics([
        "search" => $query,
        "limit" => 5
    ]);

    if (!empty($rawResults) && !isset($rawResults["error"])) {
        foreach ($rawResults as $record) {
            $formations[] = formatEtablissement($record["fields"], $record["recordid"]);
        }
    }
}

// Résultats MÉTIERS (via ESCO)
$metiers = ($query !== "" ? escoSearch($query) : []);
?>

<section class="search-container">

<?php if ($query): ?>

    <p>Vous avez recherché : <strong><?= htmlspecialchars($query) ?></strong></p>

    <!-- 🟦 FORMATIONS -->
    <h2>Formations trouvées</h2>
    <?php if ($formations): ?>
        <div class="results-grid">
            <?php foreach ($formations as $etab): ?>
                <?= renderEtablissementCard($etab); ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune formation trouvée.</p>
    <?php endif; ?>

    <!-- 🟧 MÉTIERS -->
    <h2>Métiers trouvés</h2>
    <?php if ($metiers): ?>
        <div class="results-grid">
            <?php foreach ($metiers as $m): ?>
                <?= renderMetierCard($m); ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun métier trouvé.</p>
    <?php endif; ?>

<?php else: ?>
    <p>Aucun mot-clé saisi. <a href="index.php">Retour à l'accueil</a></p>
<?php endif; ?>

</section>

<?php require "./include/footer.inc.php"; ?>
