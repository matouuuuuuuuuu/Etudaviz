<?php
	$title="Recherche de formations";
	$description="Page de recherche permettant d'accèder aux formations en fonction de mots clés";
    $h1="Résultats associés à votre recherche :";
    $query = isset($_GET['q']) ? trim($_GET['q']) : "";

    require "./include/header.inc.php";
?>

    <section class="search-container">
        <?php if (!empty($query)) : ?>
            <p>Vous avez recherché : <strong><?php echo htmlspecialchars($query); ?></strong></p>

            <div class="results">
                <div class="result-item">
                    <h3>Formation en Informatique</h3>
                    <p>Découvrez nos cursus en développement web, cybersécurité et IA.</p>
                    <a href="formations.php">Voir la formation</a>
                </div>

                <div class="result-item">
                    <h3>Orientation après le bac</h3>
                    <p>Conseils pratiques pour bien choisir son parcours post-bac.</p>
                    <a href="orientation.php">Lire l'article</a>
                </div>

                <div class="result-item">
                    <h3>À propos d’Etudaviz</h3>
                    <p>En savoir plus sur notre mission et notre accompagnement étudiant.</p>
                    <a href="apropos.php">Découvrir</a>
                </div>
            </div>
        <?php else : ?>
            <p>Aucun mot-clé saisi. <a href="index.php">Retour à l'accueil</a></p>
        <?php endif; ?>
        </section>

<?php
    require "./include/footer.inc.php";
?>