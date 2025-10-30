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
    $debouches = getDebouchesDepuisOnisep($etab['nom'] ?? $etab['fl'] ?? '');
    $title = "Détails - " . $etab['nom'];
    $h1 = $etab['nom'];
    require "./include/header.inc.php";
?>

    <section class="formation-detail">
        <div class="formation-section presentation">
    <h3>Présentation de la formation</h3>

    <?php 
        $fl = $etab['fl'] ?? '';
        $nm = $etab['nm'] ?? '';
        $tf = $etab['tf'] ?? '';
        $etablissement = $etab['etablissement'] ?? '';
        $discipline = '';

        // Si le titre de la formation contient un tiret, on récupère la partie après le premier
        if (!empty($fl) && str_contains($fl, '-')) {
            $parts = explode('-', $fl, 2);
            $discipline = trim($parts[1]);
        }
    ?>

    <p class="intro">
        <?= htmlspecialchars($nm ?: $fl ?: 'Cette formation') ?> 
        est une formation de type 
        <strong><?= htmlspecialchars($tf ?: 'non précisé') ?></strong> 
        dispensée par 
        <strong><?= htmlspecialchars($etablissement ?: 'un établissement non spécifié') ?></strong>. 
        Elle s’adresse principalement aux étudiants souhaitant développer des compétences 
        dans le domaine de 
        <?= htmlspecialchars($discipline ?: 'la discipline concernée') ?> 
        et prépare à l’obtention d’un diplôme reconnu par l’État.
    </p>

    <ul class="presentation-details">
        <?php if (!empty($etab['annee'])): ?>
            <li><strong>Année de référence :</strong> <?= htmlspecialchars($etab['annee']) ?></li>
        <?php endif; ?>

        <?php if (!empty($etab['app'])): ?>
            <li><strong>Modalité :</strong> <?= htmlspecialchars($etab['app']) ?></li>
        <?php endif; ?>

        <?php if (!empty($etab['amg'])): ?>
            <li><strong>Organisation pédagogique :</strong> 
                <?= htmlspecialchars(str_replace('|', ', ', $etab['amg'])) ?>
            </li>
        <?php endif; ?>

        <?php if (!empty($etab['aut'])): ?>
            <li><strong>Conditions d’accès :</strong> <?= htmlspecialchars($etab['aut']) ?></li>
        <?php endif; ?>

        <?php if (!empty($etab['tc'])): ?>
            <li><strong>Statut de l’établissement :</strong> <?= htmlspecialchars($etab['tc']) ?></li>
        <?php endif; ?>
    </ul>

    <div class="formation-description">
        <p>
            Cette formation permet aux étudiants d’acquérir les connaissances théoriques et pratiques nécessaires 
            pour exercer dans le secteur correspondant. Elle met l’accent sur la professionnalisation, 
            à travers des cours spécialisés, des projets et parfois des périodes de stage en entreprise.
        </p>

        <p>
            Selon le parcours et la spécialité, les débouchés peuvent inclure des postes dans 
            des entreprises, des administrations ou des établissements publics, 
            ainsi qu’une poursuite d’études vers un niveau supérieur 
            (Licence professionnelle, Master, ou école spécialisée).
        </p>
    </div>
        </div>


    </section>

    <?php if ($debouches): ?>
<section class="formation-section debouches">
    <h3>Débouchés et poursuites d’études</h3>
    <?php if (!empty($debouches['secteur'])): ?>
        <p><strong>Secteur(s) :</strong> <?= htmlspecialchars($debouches['secteur']) ?></p>
    <?php endif; ?>
    <?php if (!empty($debouches['debouches'])): ?>
        <p><strong>Métiers visés :</strong> <?= htmlspecialchars($debouches['debouches']) ?></p>
    <?php endif; ?>
    <?php if (!empty($debouches['poursuite_etudes'])): ?>
        <p><strong>Poursuites d’études :</strong> <?= htmlspecialchars($debouches['poursuite_etudes']) ?></p>
    <?php endif; ?>
</section>
<?php endif; ?>


<?php require "./include/footer.inc.php"; ?>
