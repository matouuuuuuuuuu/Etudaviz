<?php
	$title="Formations";
	$description="Page répertoriant l'ensemble des formations en fonction de plusieurs critères";
    $h1="Formations diplômantes";
    require "./include/header.inc.php";
?>

    <section class="intro">
        <p>Explorez les différentes formations disponibles après le bac : BTS, BUT, Licences, Masters… 
        Utilisez la barre de recherche ou les filtres pour trouver rapidement ce qui vous intéresse.</p>
    </section>

    <section class="search-section">
        <form class="search-bar" action="#" method="get">
            <input type="text" name="q" placeholder="Rechercher une formation (ex: BTS, Licence, BUT)...">
            <button type="submit">🔍</button>
        </form>
    </section>

    <!-- Filtres -->
    <section class="filters">
    <h2>Filtrer les formations</h2>
    <div class="filter-group">
        <label for="niveau">Niveau d’étude :</label>
        <select name="niveau" id="niveau">
            <option value="">-- Sélectionnez un niveau --</option>
            <option value="bac2">Bac +2 (BTS, DUT/BUT)</option>
            <option value="bac3">Bac +3 (Licence, BUT)</option>
            <option value="bac5">Bac +5 (Master, Écoles)</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="domaine">Domaine :</label>
        <select name="domaine" id="domaine">
            <option value="">-- Sélectionnez un domaine --</option>
            <option value="informatique">Informatique</option>
            <option value="sante">Santé</option>
            <option value="commerce">Commerce</option>
            <option value="sciences">Sciences</option>
            <option value="arts">Arts</option>
        </select>
    </div>

    <button type="reset" class="reset-btn">Réinitialiser</button>
</section>


    <!-- Liste des formations -->
    <section class="formations-list">
        <article class="formation-card">
            <h2>BTS SIO</h2>
            <p><strong>Durée :</strong> 2 ans</p>
            <p><strong>Domaine :</strong> Informatique, Gestion</p>
            <a href="#">Voir la fiche complète</a>
        </article>

        <article class="formation-card">
            <h2>Licence Droit</h2>
            <p><strong>Durée :</strong> 3 ans</p>
            <p><strong>Domaine :</strong> Droit, Sciences sociales</p>
            <a href="#">Voir la fiche complète</a>
        </article>

        <article class="formation-card">
            <h2>BUT Informatique</h2>
            <p><strong>Durée :</strong> 3 ans</p>
            <p><strong>Domaine :</strong> Informatique</p>
            <a href="#">Voir la fiche complète</a>
        </article>
    </section>

<?php
    require "./include/footer.inc.php";
?>
