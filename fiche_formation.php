<?php
require "./include/functions.inc.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Formation introuvable.");
}


$etab = getEtablissementById($id);
if (!$etab) {
    die("Aucune donnée trouvée.");
}

$title = "Détails - " . $etab['nom'];
$h1 = $etab['nom'];

require "./include/header.inc.php";
?>

<section class="formation-detail">
    <h2><?= htmlspecialchars($etab['nom']) ?></h2>
    <p><strong>Type :</strong> <?= htmlspecialchars($etab['type']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($etab['adresse']) ?></p>
    <?php if (!empty($etab['services'])): ?>
        <p><strong>Services :</strong> <?= htmlspecialchars(implode(', ', $etab['services'])) ?></p>
    <?php endif; ?>
    <p><strong>Ouverture :</strong> <?= htmlspecialchars($etab['ouverture']) ?></p>
    <p><strong>Région :</strong> <?= htmlspecialchars($etab['region']) ?></p>
    <p><strong>Académie :</strong> <?= htmlspecialchars($etab['academie']) ?></p>
</section>

<?php require "./include/footer.inc.php"; ?>
