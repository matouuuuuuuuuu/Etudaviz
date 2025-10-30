<?php

function incrementCounter(): int {
    $file = __DIR__ . '/counter.txt';
    if (!file_exists($file)) file_put_contents($file, '0');

    $visites = (int)file_get_contents($file);
    $visites++;
    file_put_contents($file, $visites);
    return $visites;
}


function getCurrentDate(string $format = "d/m/Y"): string {
    return date($format);
}

/**
 * Construit les paramètres d'appel à l'API Parcoursup (fr-esr-cartographie_formations_parcoursup)
 * avec gestion étendue du type "Université" qui regroupe plusieurs sous-types (Licence, BUT, etc.)
 *
 * @param array $options
 * @return array
 */
function buildEtablissementsApiParams(array $options = []): array
{
    $params = [
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'rows'    => $options['limit'] ?? 100,
        'facet'   => ['region', 'departement', 'commune', 'tf']
    ];

    // Pagination
    if (!empty($options['offset'])) {
        $params['start'] = (int)$options['offset'];
    }

    // Recherche libre
    if (!empty($options['search'])) {
        $params['q'] = $options['search'];
    }

    // Filtres région / département / commune
    $filtres = [
        'region'      => 'region',
        'departement' => 'departement',
        'commune'     => 'ville'
    ];

    foreach ($filtres as $apiField => $userParam) {
        if (!empty($options[$userParam])) {
            $params["refine.$apiField"] = $options[$userParam];
        }
    }

    // --- Gestion du "type" ---
    if (!empty($options['type'])) {
        $type = trim($options['type']);

        // Types valides trouvés dans le dataset
        $typeMap = [
            "BTS - BTSA - BTSM"                        => ["BTS - BTSA - BTSM"],
            "Formations des écoles d’ingénieurs"       => ["Formations des écoles d’ingénieurs"],
            "Formations du travail social"             => ["Formations diplômantes du travail social"],
            "Université"                               => [
                "Licence",
                "Licence sélective",
                "Licence professionnelle",
                "DEUST",
                "Diplômes d'université ou d'établissement",
                "BUT",
                "DUT",
                "C.M.I - Cursus Master en Ingénierie",
                "I.A.E - Instituts d'administration des entreprises",
                "Formations d'architecture, du paysage et du patrimoine"
            ]
        ];

        if (isset($typeMap[$type])) {
            foreach ($typeMap[$type] as $tfValue) {
                $params["refine.tf[]"] = $tfValue; // permet plusieurs facettes
            }
        } else {
            // Recherche libre si non reconnu
            $params['q'] = trim(($params['q'] ?? '') . ' ' . $type);
        }
    }

    return $params;
}





/**
 * Effectue un appel HTTP à une API Open Data et renvoie la réponse décodée en tableau associatif.
 *
 * Cette fonction utilise cURL pour interroger une URL d’API publique
 * (par exemple : jeux de données ONISEP ou Enseignement supérieur).
 * Elle gère automatiquement les erreurs réseau et les erreurs de décodage JSON.
 *
 * @param string $url  URL complète de l’API à interroger.
 *
 * @return array       Tableau associatif contenant :
 *                     - les données JSON décodées en cas de succès ;
 *                     - ou un élément ['error' => 'message d’erreur'] en cas d’échec.
 *
 * @author  Étudaviz
 * @version 2.0
 */
function callOpenDataApi(string $url): array {
        $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }
    curl_close($ch);
    $data = json_decode($response, true);
    return is_array($data)
        ? $data
        : ['error' => 'Réponse invalide de l’API (JSON mal formé ou vide).'];
}


/**
 * Récupère une liste de formations issues du jeu de données ONISEP.
 *
 * Cette fonction interroge le jeu de données public « fr-esr-onisep » hébergé sur
 * data.education.gouv.fr. Elle prend en compte les paramètres générés par
 * {@see buildEtablissementsApiParams()} et retourne un tableau de résultats
 * correspondant aux formations disponibles (universités, BTS, CPGE, IUT, etc.).
 *
 * Exemple d’utilisation :
 * ```php
 * $formations = getEtablissementsSupPublics([
 *     'search' => 'informatique',
 *     'region' => 'Hauts-de-France',
 *     'limit'  => 10
 * ]);
 * if (isset($formations['error'])) {
 *     echo $formations['error'];
 * } else {
 *     print_r($formations);
 * }
 * ```
 *
 * @param array $options Tableau associatif des filtres à appliquer :
 *                       - string 'search'      : mots-clés recherchés (optionnel)
 *                       - string 'region'      : région ciblée (optionnel)
 *                       - string 'departement' : département ciblé (optionnel)
 *                       - string 'ville'       : commune ciblée (optionnel)
 *                       - string 'type'        : type de formation (BTS, Licence, etc.) (optionnel)
 *                       - int    'limit'       : nombre maximal de résultats à retourner (défaut : 100)
 *                       - int    'offset'      : décalage pour la pagination (optionnel)
 *
 * @return array Tableau associatif des enregistrements renvoyés par l’API ONISEP,
 *               ou ['error' => 'message'] en cas d’échec de la requête.
 *
 * @author  Étudaviz
 * @version 2.0 Migration vers le dataset ONISEP
 */
function getEtablissementsSupPublics(array $options = []): array
{
    $params = buildEtablissementsApiParams($options);
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query($params);
    $data = callOpenDataApi($url);

    if (isset($data['error'])) {
        return ['error' => $data['error']];
    }

    return $data['records'] ?? ['error' => 'Aucune donnée reçue depuis l’API Parcoursup.'];
}



/**
 * Récupère la liste des régions disponibles dans le jeu de données ONISEP.
 *
 * Cette fonction interroge le dataset public « fr-esr-onisep » pour extraire
 * l’ensemble des régions présentes dans les formations référencées.
 * Elle repose sur la facette "region" de l’API et renvoie un tableau simple
 * contenant les noms des régions (chaînes de caractères).
 *
 * @return array Liste des régions extraites du dataset ONISEP.
 *               Retourne un tableau vide si la requête échoue ou qu’aucune région n’est trouvée.
 *
 * @author  Étudaviz
 * @version 2.0 Migration vers l’API ONISEP
 */
function getRegionsDepuisAPI(): array
{
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query([
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'rows' => 0,
        'facet' => 'region'
    ]);

    $response = @file_get_contents($url);
    if (!$response) {
        return [];
    }

    $data = json_decode($response, true);
    if (!isset($data['facet_groups'][0]['facets'])) {
        return [];
    }

    return array_column($data['facet_groups'][0]['facets'], 'name');
}



/**
 * Charge la liste des départements correspondant à une région donnée à partir d’un fichier CSV.
 *
 * Le fichier CSV doit contenir au minimum trois colonnes : un identifiant, le nom du département
 * et le nom de la région. Cette fonction est utilisée pour alimenter dynamiquement les filtres
 * de recherche par région et département.
 *
 * @param string $regionName Nom exact de la région à filtrer.
 * @param string $csvPath    Chemin absolu ou relatif vers le fichier CSV source.
 *
 * @return array Liste des départements correspondant à la région spécifiée.
 *               Retourne un tableau vide si le fichier est introuvable ou invalide.
 *
 * @author  Étudaviz
 * @version 2.0
 */
function loadDepartements(string $regionName, string $csvPath): array
{
    $departements = [];
    if (!file_exists($csvPath) || !is_readable($csvPath)) {
        return $departements;
    }
    if (($handle = fopen($csvPath, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($data) < 3) continue;
            $departement = trim($data[1]);
            $region = trim($data[2]);
            if ($region === $regionName) {
                $departements[] = $departement;
            }
        }
        fclose($handle);
    }
    return $departements;
}




/**
 * Génère le code HTML d’une carte représentant un établissement ou une formation.
 *
 * Cette fonction crée dynamiquement un bloc HTML contenant les informations
 * principales d’un établissement (nom, type, adresse, services, date d’ouverture...).
 * Elle est conçue pour être utilisée dans les boucles d’affichage des résultats
 * de recherche ou de listing.
 *
 * @param array $etab Tableau associatif contenant les informations de l’établissement :
 *                    - string 'id'        : identifiant unique
 *                    - string 'nom'       : nom de l’établissement
 *                    - string 'type'      : type d’établissement
 *                    - string 'adresse'   : adresse complète
 *                    - array  'services'  : liste des services disponibles (optionnel)
 *                    - string 'ouverture' : date d’ouverture (optionnel)
 *
 * @return string Code HTML prêt à être inséré dans la page.
 *
 * @author  Étudaviz
 * @version 2.0
 */
function renderEtablissementCard(array $etab): string
{
    $html = '<div class="etab-card">';
    $html .= '<h4><a href="fiche_formation.php?id=' . urlencode($etab['id']) . '">'
           . htmlspecialchars($etab['nom']) . '</a></h4>';
    $html .= '<p><strong>Type :</strong> ' . htmlspecialchars($etab['type']) . '</p>';
    $html .= '<p><strong>Adresse :</strong> ' . htmlspecialchars($etab['adresse']) . '</p>';

    if (!empty($etab['services'])) {
        $html .= '<p><strong>Services :</strong> ' . htmlspecialchars(implode(', ', $etab['services'])) . '</p>';
    }

    if (!empty($etab['ouverture'])) {
        $html .= '<p><strong>Ouverture :</strong> ' . htmlspecialchars($etab['ouverture']) . '</p>';
    }

    $html .= '</div>';
    return $html;
}



/**
 * Formate les données issues du dataset « fr-esr-cartographie_formations_parcoursup »
 * pour les rendre exploitables par l’interface d’affichage.
 *
 * Cette fonction traduit les clés réelles du jeu de données Parcoursup
 * (formation, établissement, localisation, etc.) en un tableau normalisé.
 *
 * Exemple d’utilisation :
 * ```php
 * $formation = formatEtablissement($record['fields'], $record['recordid']);
 * echo $formation['nom'] . ' - ' . $formation['etablissement'];
 * ```
 *
 * @param array  $fields    Données "fields" issues d’un enregistrement API.
 * @param string $recordid  Identifiant unique du record (facultatif).
 *
 * @return array Tableau associatif normalisé contenant :
 *               - string 'id'            : identifiant unique (recordid)
 *               - string 'nom'           : intitulé principal de la formation
 *               - string 'type'          : type ou catégorie de la formation (BTS, Licence, etc.)
 *               - string 'etablissement' : nom de l’établissement
 *               - string 'adresse'       : ville, département et région concaténés
 *               - string 'lien'          : lien vers la fiche Parcoursup
 *               - string 'site'          : site web de l’établissement (si disponible)
 *               - string 'ville'         : commune
 *               - string 'departement'   : département
 *               - string 'region'        : région
 *               - array  'coordonnees'   : coordonnées GPS (si disponibles)
 *
 * @author  Étudaviz
 * @version 2.1 Adaptation au dataset Parcoursup
 */
function formatEtablissement(array $fields, string $recordid = null): array
{
    $nom = $fields['fl']
        ?? $fields['nm']
        ?? 'Nom inconnu';

    $type = $fields['tf']
        ?? 'Type inconnu';

    $etablissement = $fields['etab_nom']
        ?? 'Établissement non précisé';

    $adresseParts = [];
    if (!empty($fields['commune'])) $adresseParts[] = $fields['commune'];
    if (!empty($fields['departement'])) $adresseParts[] = $fields['departement'];
    if (!empty($fields['region'])) $adresseParts[] = $fields['region'];
    $adresse_complete = implode(', ', $adresseParts);

    return [
        'id'            => $recordid ?? uniqid('formation_'),
        'nom'           => $nom,
        'type'          => $type,
        'etablissement' => $etablissement,
        'adresse'       => $adresse_complete ?: 'Adresse inconnue',
        'ville'         => $fields['commune'] ?? '',
        'departement'   => $fields['departement'] ?? '',
        'region'        => $fields['region'] ?? '',
        'site'          => $fields['etab_url'] ?? '',
        'lien'          => $fields['fiche'] ?? '',
        'coordonnees'   => $fields['etab_gps'] ?? null,
        'annee'         => $fields['annee'] ?? '',
        'code_formation'=> $fields['code_formation'] ?? '',
        'apprentissage' => $fields['app'] ?? '',
        'aut'           => $fields['aut'] ?? '',
    ];
}





/**
 * Récupère les informations détaillées d’une formation à partir de son identifiant ONISEP.
 *
 * Cette fonction interroge le dataset public « fr-esr-onisep » hébergé sur
 * data.education.gouv.fr afin d’obtenir les détails d’une formation spécifique.
 * L’identifiant attendu correspond généralement au champ `recordid` renvoyé par
 * {@see getEtablissementsSupPublics()}.
 *
 *
 * @param string $id  Identifiant unique de la formation (recordid ONISEP).
 *
 * @return array|null Tableau associatif contenant les informations détaillées de la formation,
 *                    ou null si aucune correspondance n’est trouvée.
 *
 * @author  Étudaviz
 * @version 2.0 Migration vers le dataset ONISEP
 */
function getEtablissementById(string $id): ?array {
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query([
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'q' => "recordid:$id",
        'rows' => 1
    ]);

    $data = callOpenDataApi($url);
    if (!empty($data['records'][0]['fields'])) {
        $record = $data['records'][0];
        return formatEtablissement($record['fields'], $record['recordid']);
    }
    return null;
}


function getDebouchesDepuisOnisep(string $intitule): ?array {
    $url = "https://data.education.gouv.fr/api/records/1.0/search?" . http_build_query([
        'dataset' => 'fr-esr-onisep',
        'q' => $intitule,
        'rows' => 1
    ]);

    $data = callOpenDataApi($url);
    if (!isset($data['records'][0]['fields'])) {
        return null;
    }

    $fields = $data['records'][0]['fields'];
    return [
        'secteur' => $fields['secteur'] ?? null,
        'debouches' => $fields['debouches'] ?? null,
        'poursuite_etudes' => $fields['poursuite_etudes'] ?? null,
    ];
}






?>
