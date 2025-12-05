<?php
require "./include/functions.inc.php";

$uri = $_GET["uri"] ?? null;
if (!$uri) die("Métier introuvable.");

$metier = escoGetMetier($uri);
if (!$metier) die("Aucune donnée disponible pour ce métier.");

$title = "Métier : " . htmlspecialchars($metier["title"]);
$h1 = htmlspecialchars($metier["title"]);

require "./include/header.inc.php";
?>

<section class="formation-detail">

    <div class="formation-section presentation">
        <h3>Présentation du métier</h3>

        <?php if (!empty($metier["description"])): ?>
            <p><?= nl2br(htmlspecialchars($metier["description"])) ?></p>
        <?php else: ?>
            <p>Aucune description disponible.</p>
        <?php endif; ?>

        <ul class="presentation-details">
            <?php if (!empty($metier["isco"])): ?>
                <li><strong>Code ISCO :</strong> <?= htmlspecialchars($metier["isco"]) ?></li>
            <?php endif; ?>

            <?php if (!empty($metier["altLabels"])): ?>
                <li><strong>Aussi appelé :</strong> 
                    <?= htmlspecialchars(implode(', ', $metier["altLabels"])) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>


    <?php if (!empty($metier["skillsEssential"])): ?>
        <div class="formation-section debouches">
            <h3>Compétences essentielles</h3>
            <ul>
                <?php foreach ($metier["skillsEssential"] as $skill): ?>
                    <li><?= htmlspecialchars($skill) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($metier["skillsOptional"])): ?>
        <div class="formation-section debouches">
            <h3>Compétences optionnelles</h3>
            <ul>
                <?php foreach ($metier["skillsOptional"] as $skill): ?>
                    <li><?= htmlspecialchars($skill) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</section>

<?php require "./include/footer.inc.php"; ?>
