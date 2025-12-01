<?php
require "./include/functions.inc.php";

// Récupération des filtres utilisateur
$regionChoisie = $_GET['region'] ?? '';
$departementChoisi = $_GET['departement'] ?? '';
$typeChoisi = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';
$limit = 6; // <= essentiel pour le test du bouton

// Données pour le formulaire
$regions = getRegionsDepuisAPI();

$departementsDisponibles = $departementsParRegion[$regionChoisie] ?? [];

$etablissements = getEtablissementsSupPublics([
    'limit' => $limit,
    'region' => $regionChoisie,
    'departement' => $departementChoisi,
    'type' => $typeChoisi,
    'search' => $search
]);


// Traitement des résultats
$resultats = [];
$messageErreur = null;

if (isset($etablissements['error'])) {
    $messageErreur = $etablissements['error'];
} elseif (empty($etablissements)) {
    $messageErreur = "Aucun établissement trouvé.";
} else {
    foreach ($etablissements as $record) {
        $resultats[] = formatEtablissement($record['fields'], $record['recordid']);    
    }
}

$title = "Formations diplômantes - Etudaviz";
$description = "Page répertoriant l'ensemble des formations en fonction de plusieurs critères";
$h1 = "Formations diplômantes";
$canonical = "https://etudaviz.alwaysdata.net/formations.php";

require "./include/header.inc.php";
?>


    <section>
         <!-- 🟪 Barre de recherche centrale -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Rechercher une formation, une ville..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Rechercher</button>
            </form>

            <button id="toggle-filters" class="filter-toggle">
                <span class="icon">⚙️</span> Filtres
            </button>
        </div>

        <div class="layout">
            <div id="filters-modal" class="modal hidden">
                <div class="modal-content">
                    <button id="close-filters" class="close-button">&times;</button>

                    <h3>Filtres sélectionnés</h3>
                    <ul>
                        <?php if ($regionChoisie): ?>
                            <li><strong>Région :</strong> <?= htmlspecialchars($regionChoisie) ?></li>
                        <?php endif; ?>
                        <?php if ($departementChoisi): ?>
                            <li><strong>Département :</strong> <?= htmlspecialchars($departementChoisi) ?></li>
                        <?php endif; ?>
                        <?php if ($typeChoisi): ?>
                            <li><strong>Type :</strong> <?= htmlspecialchars($typeChoisi) ?></li>
                        <?php endif; ?>
                        <?php if (!$regionChoisie && !$departementChoisi && !$typeChoisi): ?>
                            <li>Aucun filtre sélectionné</li>
                        <?php endif; ?>
                    </ul>

                    <form method="GET" action="">
                        <label for="region">Région :</label>
                        <select name="region" id="region">
                            <option value="">-- Toutes les régions --</option>
                            <?php foreach ($regions as $region): ?>
                                <option value="<?= htmlspecialchars($region) ?>" <?= $region === $regionChoisie ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($region) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="departement">Département :</label>
                        <select name="departement" id="departement">
                            <option value="">-- Tous les départements --</option>
                            <?php foreach ($departementsDisponibles as $dep): ?>
                                <option value="<?= htmlspecialchars($dep) ?>" <?= $dep === $departementChoisi ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="type">Type d’établissement :</label>
                        <select name="type" id="type">
                            <option value="">-- Tous --</option>
                            <?php foreach (['Formations en université', "Formations des écoles d’ingénieurs", 'BTS - BTSA - BTSM', 'Formations du travail social'] as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $type === $typeChoisi ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        
                        <div class="form-actions" style="margin-top: 20px; display: flex; justify-content: space-between;">
                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="reset-button">Réinitialiser</a>
                            <button type="submit" class="apply-button">Appliquer les filtres</button>
                        </div>

                    </form>
                </div>
            </div>
            <?php if ($messageErreur): ?>
                <p><?= htmlspecialchars($messageErreur) ?></p>
            <?php else: ?>

                <ul id="etablissement-list">
                    <?php foreach ($resultats as $etab): ?>
                        <?= renderEtablissementCard($etab) ?>
                    <?php endforeach; ?>
                </ul>
                <?php if (count($resultats) === $limit): ?>
                    <div class="results">
                        <button id="voir-plus" data-page="2">Voir plus</button>
                    </div>
                <?php endif; ?>   
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('voir-plus');
            const list = document.getElementById('etablissement-list');

            if (!button || !list) return;

            button.addEventListener('click', function () {
                const page = parseInt(button.dataset.page) || 2;

                // Met à jour le texte du bouton pendant le chargement
                button.disabled = true;
                button.textContent = "Chargement...";

                // Construit l'URL avec tous les filtres actuels + nouvelle page
                const params = new URLSearchParams(window.location.search);
                params.set('page', page);

                fetch('load-etablissements.php?' + params.toString())
                    .then(res => res.text())
                    .then(html => {
                        // Injecte les nouveaux résultats
                        list.insertAdjacentHTML('beforeend', html);

                        // Réactive le bouton si des résultats existent
                        if (html.trim() !== '') {
                            button.dataset.page = page + 1;
                            button.disabled = false;
                            button.textContent = "Voir plus";
                        } else {
                            // Sinon on masque le bouton
                            button.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des établissements :', error);
                        button.disabled = true;
                        button.textContent = "Erreur de chargement";
                    });
            });
        });
    </script>

   <script>
    document.addEventListener('DOMContentLoaded', function () {
        const openButton = document.getElementById('toggle-filters');
        const modal = document.getElementById('filters-modal');
        const closeButton = document.getElementById('close-filters');

        if (openButton && modal && closeButton) {
            openButton.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });

            closeButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            // Fermer la modale si clic en dehors du contenu
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regionSelect = document.getElementById('region');
            const departementSelect = document.getElementById('departement');
            if (!regionSelect || !departementSelect) return;
            regionSelect.addEventListener('change', function () {
                const selectedRegion = regionSelect.value;
                departementSelect.innerHTML = '<option value="">-- Tous les départements --</option>';
                if (!selectedRegion) return;
                fetch('get-departements.php?region=' + encodeURIComponent(selectedRegion))
                    .then(response => response.json())
                    .then(departements => {
                        departements.forEach(dep => {
                            const option = document.createElement('option');
                            option.value = dep;
                            option.textContent = dep;
                            departementSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erreur de chargement des départements :', error);
                    });
            });
        });
    </script>



<?php require "./include/footer.inc.php"; ?>
