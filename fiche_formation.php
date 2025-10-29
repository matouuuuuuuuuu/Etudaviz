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
    <p><strong>Établissement :</strong> <?= htmlspecialchars($etab['etablissement']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($etab['adresse']) ?></p>

    <?php if (!empty($etab['region'])): ?>
        <p><strong>Région :</strong> <?= htmlspecialchars($etab['region']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['departement'])): ?>
        <p><strong>Département :</strong> <?= htmlspecialchars($etab['departement']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['ville'])): ?>
        <p><strong>Commune :</strong> <?= htmlspecialchars($etab['ville']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['annee'])): ?>
        <p><strong>Année :</strong> <?= htmlspecialchars($etab['annee']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['aut'])): ?>
        <p><strong>Conditions d’accès :</strong> <?= htmlspecialchars($etab['aut']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['apprentissage'])): ?>
        <p><strong>Apprentissage :</strong> <?= htmlspecialchars($etab['apprentissage']) ?></p>
    <?php endif; ?>

    <?php if (!empty($etab['site'])): ?>
        <p><strong>Site web :</strong> <a href="<?= htmlspecialchars($etab['site']) ?>" target="_blank"><?= htmlspecialchars($etab['site']) ?></a></p>
    <?php endif; ?>

    <?php if (!empty($etab['lien'])): ?>
        <p><strong>Fiche Parcoursup :</strong> <a href="<?= htmlspecialchars($etab['lien']) ?>" target="_blank">Voir sur Parcoursup ↗</a></p>
    <?php endif; ?>
</section>

<?php require "./include/footer.inc.php"; ?>
