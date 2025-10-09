<?php
  $title = "Accueil";
  $description = "Etudaviz - Trouve ta voie : découvre les formations, les témoignages étudiants et les guides d’orientation pour construire ton avenir.";
  $h1 = "Bienvenue sur Etudaviz";
  require "./include/header.inc.php";
?>

    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-text">
            <h2>Choisis ton avenir, pas par hasard </h2>
                <p>
                    Etudaviz t’aide à <strong>découvrir des formations</strong>,
                    <strong>comparer les parcours</strong> et lire les
                    <strong>témoignages d’étudiants</strong> pour trouver la voie qui te correspond.
                </p>
                <div class="hero-buttons">
                    <a href="formations.php" class="btn-primary">Explorer les formations</a>
                    <a href="orientation.php" class="btn-secondary">Trouver ma voie</a>
                </div>
            </div>

            <div class="hero-image">
                <img src="./images/construction.png" alt="Jeunes étudiants motivés découvrant leur avenir">
            </div>
        </div>
    </section>


    <section class="services">
        <h2 class="services-title">Explore les rubriques clés 🧭</h2>
        <div class="service-block">
                <div class="service-text">
                <h3>Formations</h3>
                    <p>Découvre les parcours post-bac : BUT, BTS, licences, écoles spécialisées, alternance…</p>
                    <a href="formations.php" class="btn-link">Voir les formations →</a>
                </div>
                <div class="service-image">
                    <img src="./images/formations.jpg" alt="Salle de cours avec étudiants">
                </div>
        </div>

        <div class="service-block">
            <div class="service-image">
                <img src="./images/orientation.jpg" alt="Conseil orientation">
            </div>
            <div class="service-text">
                <h3>Orientation</h3>
                    <p>Mieux te connaître, comprendre les débouchés, poser les bonnes questions à temps.</p>
                    <a href="orientation.php" class="btn-link">En savoir plus →</a>
            </div>
        </div>

        <div class="service-block">
            <div class="service-text">
                <h3>Avis d’étudiants</h3>
                    <p>Lis des témoignages honnêtes d’autres jeunes et partage ton retour d’expérience.</p>
                    <a href="avis.php" class="btn-link">Découvrir les avis →</a>
            </div>
            <div class="service-image">
                <img src="./images/community.jpg" alt="Communauté étudiante">
            </div>
        </div>
    </section>

    <section class="avis">
        <h2>Ce qu’en disent les étudiants 🎓</h2>
        <div class="slideshow-container">
            <div class="mySlides fade">
                <span class="quote-mark">“</span>
                <p class="quote">
                Grâce à Etudaviz, j’ai découvert une école d’ingénieur qui correspondait parfaitement à mon profil !
                </p>
                <p class="author">— Léo, ancien lycéen</p>
            </div>

            <div class="mySlides fade">
                <span class="quote-mark">“</span>
                <p class="quote">
                Les avis des autres étudiants m’ont vraiment aidée à choisir ma licence pro en communication.
                </p>
                <p class="author">— Inès, étudiante à CY</p>
            </div>

            <div class="mySlides fade">
                <span class="quote-mark">“</span>
                <p class="quote">
                J’ai pu comparer les parcours et trouver une formation en alternance près de chez moi.
                </p>
                <p class="author">— Thomas, étudiant en BTS</p>
            </div>

            <div class="slider-controls">
            <span class="prev" onclick="plusSlides(-1)">&#10094;</span>
            <span class="next" onclick="plusSlides(1)">&#10095;</span>
            </div>
        </div>
    </section>




<!-- CTA FINAL -->
<section id="cta">
  <h2>Prêt à trouver ta voie ?</h2>
  <p>Pars à la découverte de ton futur dès maintenant 👇</p>
  <nav class="cta-links">
    <a href="formations.php" class="btn">Formations</a>
    <a href="avis.php" class="btn">Avis étudiants</a>
    <a href="guides.php" class="btn">Guides</a>
    <a href="contact.php" class="btn">Contact</a>
  </nav>
</section>


<!-- À PROPOS -->
<section id="about-project">
  <h2>À propos d’<span class="highlight">Etudaviz</span></h2>
  <p>
    Etudaviz est un projet réalisé dans le cadre de la mineure <strong>Développement Web Avancé</strong> 
    à <em>CY Cergy Paris Université</em>.  
    Notre mission : offrir un site simple, fiable et motivant pour aider chaque lycéen 
    à construire son avenir avec confiance 💪
  </p>
</section>


<script type="module" src="/js/slides.js"></script>

<?php
  require "./include/footer.inc.php";
?>
