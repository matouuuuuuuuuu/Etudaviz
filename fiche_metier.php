<?php
require "./include/functions.inc.php";

$uri = $_GET["uri"] ?? null;
if (!$uri) {
    header("Location: index.php?error=metier_introuvable");
    exit;
}
$metier = escoGetMetier($uri);
if (!$metier) {
    header("Location: index.php?error=metier_inexistant");
    exit;
}

$title = "Métier : " . htmlspecialchars($metier["title"]);
$h1 = ucfirstUtf8(htmlspecialchars($metier["title"]));

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

    <?php if (!empty($metier["skillsEssential"]) || !empty($metier["skillsOptional"])): ?>
        <div class="metier-skill-columns">

            <?php if (!empty($metier["skillsEssential"])): ?>
                <div class="metier-skill-card">
                    <h3 class="metier-skill-title">Compétences essentielles</h3>
                    <ul class="metier-skill-list">
                        <?php foreach ($metier["skillsEssential"] as $skill): ?>
                            <li><?= htmlspecialchars(ucfirst($skill)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($metier["skillsOptional"])): ?>
                <div class="metier-skill-card">
                    <h3 class="metier-skill-title">Compétences optionnelles</h3>
                    <ul class="metier-skill-list">
                        <?php foreach ($metier["skillsOptional"] as $skill): ?>
                            <li><?= htmlspecialchars(ucfirst($skill)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>




</section>

<?php require "./include/footer.inc.php"; ?>
