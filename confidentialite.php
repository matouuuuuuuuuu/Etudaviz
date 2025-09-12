<?php
	$title="Confidentialité";
	$description="Page repertoriant notre politique de confidentialité";
    $h1="Politique de confidentialité";
    require "./include/header.inc.php";
?>
    <main class="privacy">

        <p>La présente politique de confidentialité a pour objectif d’informer les utilisateurs du site sur la manière dont leurs données personnelles peuvent être collectées et traitées.</p>

        <h2>1. Responsable du traitement</h2>
        <p>Ce site est édité dans le cadre d’un projet étudiant.  
        Responsable de publication : [Dihya Mokri, Loris Beguin, Léa Bonacorsi, Mathis Albrun].  
        </p>

        <h2>2. Données collectées</h2>
        <p>Lors de votre navigation sur ce site, les informations suivantes peuvent être collectées :</p>
        <ul>
            <li>Adresse IP (à des fins de statistiques et de sécurité) ;</li>
            <li>Données de navigation (pages visitées, temps passé, etc.) ;</li>
            <li>Informations que vous fournissez volontairement via un formulaire de contact (nom, prénom, email, message).</li>
        </ul>

        <h2>3. Finalité de la collecte</h2>
        <p>Les données collectées sont utilisées uniquement pour :</p>
        <ul>
            <li>Améliorer l’expérience utilisateur et les performances du site ;</li>
            <li>Répondre aux messages envoyés via le formulaire de contact ;</li>
            <li>Établir des statistiques de fréquentation.</li>
        </ul>

        <h2>4. Conservation des données</h2>
        <p>Les données personnelles sont conservées pour une durée maximale de 3 ans, sauf obligation légale contraire.</p>

        <h2>5. Partage des données</h2>
        <p>Vos données ne sont en aucun cas vendues ou transmises à des tiers, sauf si la loi l’exige.</p>

        <h2>6. Cookies</h2>
        <p>Ce site peut utiliser des cookies afin de mesurer l’audience et améliorer la navigation.  
        Vous pouvez à tout moment refuser ou supprimer les cookies via les paramètres de votre navigateur.</p>

        <h2>7. Vos droits</h2>
        <p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez des droits suivants :</p>
        <ul>
            <li>Droit d’accès, de rectification et de suppression de vos données ;</li>
            <li>Droit à la limitation ou opposition au traitement ;</li>
            <li>Droit à la portabilité de vos données.</li>
        </ul>

        <h2>8. Sécurité</h2>
        <p>Nous mettons en œuvre les mesures techniques et organisationnelles nécessaires pour protéger vos données contre tout accès non autorisé, perte ou divulgation.</p>

        <h2>9. Modification de la politique</h2>
        <p>Cette politique de confidentialité peut être mise à jour en fonction de l’évolution du site ou de la législation. La date de mise à jour sera indiquée en bas de la page.</p>

        <p><em>Dernière mise à jour : <?= date("d/m/Y"); ?></em></p>
    </main>
<?php
    require "./include/footer.inc.php";
?>
