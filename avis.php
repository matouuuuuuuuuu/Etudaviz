<?php
require "./include/functions.inc.php";

/* ---------------------------------------------------------
   META
--------------------------------------------------------- */
$title = "Avis sur les formations - Etudaviz";
$description = "Consultez les avis laissés par les étudiants sur les formations.";
$h1 = "Avis des étudiants";
$canonical = "https://etudaviz.alwaysdata.net/avis.php";

require "./include/header.inc.php";

/* ---------------------------------------------------------
   RÉCUPÉRATION DES AVIS
--------------------------------------------------------- */
$avis = getAvisParFormation(); // Fonction que tu as dans functions.inc.php

/* ---------------------------------------------------------
   REGROUPEMENT PAR FORMATION
--------------------------------------------------------- */
$avisParFormation = [];

foreach ($avis as $a) {
    $idFormation = $a['id_formation'];
    $avisParFormation[$idFormation][] = $a;
}

$formationsNoms = [];

foreach (array_keys($avisParFormation) as $idFormation) {
    $etab = getEtablissementById($idFormation);
    $formationsNoms[$idFormation] = $etab['nom'] ?? 'Formation inconnue';
}
?>

<section class="avis-container">

    <h2 class="avis-main-title">Tous les avis par formation</h2>

    <?php if (empty($avisParFormation)): ?>
        <p class="no-result">Aucun avis disponible pour le moment.</p>
    <?php endif; ?>

    <?php foreach ($avisParFormation as $idFormation => $listeAvis): ?>

        <section class="avis-bloc-formation">

            <h3 class="avis-formation-title">Formation : <?= htmlspecialchars($formationsNoms[$idFormation]) ?></h3>

            <div class="avis-grid">

                <?php foreach ($listeAvis as $a): ?>

                    <article class="avis-card">

                        <h4 class="avis-title">
                            <?= htmlspecialchars($a['titre_avis']) ?>
                        </h4>

                        <p class="avis-meta">
                            Par <strong><?= htmlspecialchars($a['auteur']) ?></strong> — 
                            Expérience : <?= htmlspecialchars($a['date_experience']) ?>
                        </p>

                        <p class="avis-description">
                            <?= nl2br(htmlspecialchars($a['description'])) ?>
                        </p>

                        <div class="avis-footer">
                            <span class="avis-likes">❤️ <?= (int)$a['likes'] ?></span>
                            <span class="avis-date">
                                Publié le <?= htmlspecialchars(date("d/m/Y", strtotime($a['date_publication']))) ?>
                            </span>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endforeach; ?>

</section>

<?php require "./include/footer.inc.php"; ?>
