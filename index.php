<?php
	$title = "Accueil";
	$description = "Page d'accueil d'Etudaviz permettant aux lycéens de découvrir des formations, des avis étudiants et des guides pour leur orientation.";
    $h1 = "Bienvenue sur Etudaviz";
    require "./include/header.inc.php";
?>

<main>
     <section class="hero">
        <div class="hero-text">
            <h2>Prépare ton avenir dès aujourd’hui 🚀</h1>
            <p>
                Explore les <strong>formations disponibles</strong>, découvre des <strong>avis d’étudiants</strong> 
                et accède à des <strong>guides pratiques</strong> pour bien choisir ton futur parcours.
            </p>
            <a href="formations.php" class="btn-primary">Découvrir les formations</a>
        </div>
        <div class="hero-img">
            <img src="images/etudiants.jpg" alt="Étudiants en orientation">
        </div>
    </section>

     <section class="services">
        <h2>Nos Services</h2>
        <div class="services-grid">
            <div class="card">
                <img src="images/formations.jpg" alt="Formations">
                <h3>Formations</h3>
                <p>Un catalogue complet de formations pour t’aider à trouver ta voie.</p>
                <a href="formations.php">En savoir plus →</a>
            </div>
            <div class="card">
                <img src="images/orientation.jpg" alt="Orientation">
                <h3>Orientation</h3>
                <p>Des conseils pratiques et outils d’aide à l’orientation adaptés aux lycéens.</p>
                <a href="orientation.php">En savoir plus →</a>
            </div>
            <div class="card">
                <img src="images/community.jpg" alt="Communauté">
                <h3>Avis d’étudiants</h3>
                <p>Découvre les retours d’expérience d’autres étudiants sur leurs parcours.</p>
                <a href="avis.php">En savoir plus →</a>
            </div>
        </div>
    </section>

    <!-- Section fonctionnalités -->
    <section id="features">
        <h2>Ce que tu trouveras sur Etudaviz</h2>
        <ul>
            <li><strong>Fiches Formations</strong> : découvre les parcours possibles après le bac.</li>
            <li><strong>Avis d’étudiants</strong> : profite de témoignages réels pour te projeter.</li>
            <li><strong>Guides pratiques</strong> : conseils et astuces pour réussir ton orientation.</li>
            <li><strong>Ressources utiles</strong> : liens et documents pour approfondir.</li>
        </ul>
    </section>


        <!-- testtetstststeettetstststs-->

    <!-- Section CTA (appel à l’action) -->
    <section id="cta">
        <h2>Commence ton exploration</h2>
        <p>Accède directement aux rubriques principales :</p>
        <nav class="cta-links">
            <a href="formations.php" class="btn">Formations</a>
            <a href="avis.php" class="btn">Avis étudiants</a>
            <a href="guides.php" class="btn">Guides</a>
            <a href="contact.php" class="btn">Contact</a>
        </nav>
    </section>

    <!-- (optionnel) Section équipe ou projet universitaire -->
    <section id="about-project">
        <h2>À propos du projet</h2>
        <p>
            Ce site a été conçu dans le cadre de la mineure <strong>Développement Web Avancé</strong> 
            à <em>CY Cergy Paris Université</em>.  
            Notre objectif est d’offrir un outil simple et clair pour accompagner les lycéens dans 
            leur choix d’études supérieures.
        </p>
    </section>
</main>

<?php
    require "./include/footer.inc.php";
?>
