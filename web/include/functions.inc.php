<?php
include __DIR__ . "/../../config/bdconnect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Incrémente et retourne un compteur de visites stocké dans un fichier texte.
 *
 * Le compteur est enregistré dans `counter.txt` (dans le même dossier que ce script).
 * Si le fichier n'existe pas, il est créé avec la valeur initiale "0".
 * La fonction lit ensuite la valeur, l'incrémente, puis réécrit la nouvelle valeur.
 *
 * @return int Nombre de visites après incrémentation.
 *
 * @author Etudaviz
 * @version 1.0
 */
function incrementCounter(): int {
    $file = __DIR__ . '/counter.txt';
    if (!file_exists($file)) file_put_contents($file, '0');

    $visites = (int)file_get_contents($file);
    $visites++;
    file_put_contents($file, $visites);
    return $visites;
}

/**
 * Retourne la date courante formatée.
 *
 * @param string $format Format compatible avec `date()` (ex: "d/m/Y", "Y-m-d H:i").
 *
 * @return string Date courante sous forme de chaîne formatée.
 *
 * @author Etudaviz
 * @version 1.0
 */
function getCurrentDate(string $format = "d/m/Y"): string {
    return date($format);
}

/**
 * Met en majuscule le premier caractère d'une chaîne UTF-8.
 *
 * @param string $str Chaîne à transformer (encodée en UTF-8).
 *
 * @return string Chaîne avec la première lettre en majuscule.
 *
 * @author Etudaviz
 * @version 1.0
 */
function ucfirstUtf8(string $str): string {
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
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
 * @author  Etudaviz
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
 * @author  Etudaviz
 * @version 2.0
 */
function renderEtablissementCard(array $etab): string
{
    $nomAffiche = mb_strimwidth($etab['nom'], 0, 50, '…');

    $html = '<li class="etab-card" style="position:relative;">';

    $html .= '<h4><a href="fiche_formation.php?id=' . urlencode($etab['id']) . '">'
           . htmlspecialchars($nomAffiche) . '</a></h4>';

    if (!empty($etab['badge'])) {
        $html .= '<span class="badge-formation">'
              . htmlspecialchars($etab['badge'])
              . '</span>';
    }

    $html .= '<p><strong>Type :</strong> ' . htmlspecialchars($etab['type']) . '</p>';

    $html .= '<p><strong>Adresse :</strong> ' . htmlspecialchars($etab['adresse']) . '</p>';

    if (!empty($etab['services'])) {
        $html .= '<p><strong>Services :</strong> ' 
              . htmlspecialchars(implode(', ', $etab['services'])) 
              . '</p>';
    }

    if (!empty($etab['ouverture'])) {
        $html .= '<p><strong>Ouverture :</strong> ' 
              . htmlspecialchars($etab['ouverture']) 
              . '</p>';
    }

    $html .= '</li>';

    return $html;
}

/**
 * Génère le code HTML d’une carte “métier” pour afficher un résultat dans une liste.
 *
 * La carte affiche :
 * - le titre du métier (avec un lien vers `fiche_metier.php` via le paramètre `uri`)
 * - le code ISCO (si disponible)
 * - les compétences clés (si disponibles)
 *
 * Les champs affichés sont échappés avec `htmlspecialchars()` pour éviter les injections HTML,
 * et l’URI est encodée avec `urlencode()` pour produire une URL valide.
 *
 * @param array $m Données du métier.
 *                Clés attendues :
 *                - 'uri' (string) : URI/identifiant du métier utilisé dans l’URL
 *                - 'title' (string) : intitulé du métier
 *                - 'isco' (string|null) : code ISCO (optionnel)
 *                - 'essentialSkills' (array|null) : liste de compétences (optionnel)
 *
 * @return string HTML de la carte métier.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function renderMetierCard(array $m): string
{
    $html = '<div class="etab-card" style="position:relative;">';

    $html .= '<h3><a href="fiche_metier.php?uri=' . urlencode($m['uri']) . '">'
           . ucfirstUtf8(htmlspecialchars($m['title'])) . '</a></h3>';
    if (!empty($m['isco'])) {
        $html .= '<p><strong>Code ISCO :</strong> ' . htmlspecialchars($m['isco']) . '</p>';
    }
    if (!empty($m['essentialSkills'])) {
        $html .= '<p><strong>Compétences clés :</strong> '
               . htmlspecialchars(implode(', ', $m['essentialSkills']))
               . '</p>';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Ouvre une connexion PDO à la base de données MySQL de l’application.
 *
 * La connexion cible la base `etudaviz` sur `localhost` avec l’encodage UTF-8.
 * En cas d’échec (identifiants invalides, service indisponible, etc.), la fonction
 * renvoie `null` afin de permettre un fonctionnement en mode dégradé.
 *
 * @return PDO|null Instance PDO si la connexion réussit, sinon `null`.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=etudaviz;charset=utf8", "root", ""); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Calcule et retourne le taux de satisfaction moyen des utilisateurs.
 * @return float|null Taux de satisfaction moyen (en %) arrondi à 1 décimale, ou `null` en cas d’échec.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getTauxSatisfaction() {
    $pdo = getDBConnection();
    if (!$pdo) return null; 

    try {
        $query = $pdo->query("SELECT AVG(satisfaction) AS taux FROM avis");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return round($result['taux'], 1);
    } catch (Exception $e) {
        return null; 
    }
}

/**
 * Retourne le nombre total d’avis utilisateurs.
 *
 * @return int|null Nombre d’avis, ou null si la BDD n’est pas accessible / erreur SQL.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getNombreAvis() {
    $pdo = getDBConnection();
    if (!$pdo) return null;

    try {
        $query = $pdo->query("SELECT COUNT(*) AS total FROM avis");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Retourne le nombre total de partenaires institutionnels.
 *
 * @return int|null Nombre de partenaires, ou null si la BDD n’est pas accessible / erreur SQL.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getNombrePartenaires() {
    $pdo = getDBConnection();
    if (!$pdo) return null;

    try {
        $query = $pdo->query("SELECT COUNT(*) AS total FROM partenaires");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Construit les paramètres d'appel à l'API Parcoursup (fr-esr-cartographie_formations_parcoursup)
 * avec gestion étendue du type "Université" qui regroupe plusieurs sous-types (Licence, BUT, etc.)
 *
 * @param array $options
 * @return array
 * 
 * @author  Etudaviz
 * @version 1.0
 */
function buildEtablissementsApiParams(array $options = []): array
{
    $limit = isset($options['limit']) ? (int)$options['limit'] : 100;
    $limit = max(1, min(20, $limit)); 
    $base = [
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'rows'    => $options['limit'] ?? 100,
        'facet'   => ['region', 'departement', 'commune', 'tf'],
    ];

    if (!empty($options['offset'])) $base['start'] = (int)$options['offset'];
    if (!empty($options['search'])) {
        $base['q'] = trim(strip_tags($options['search']));
    }

    $filtres = ['region' => 'region', 'departement' => 'departement', 'commune' => 'ville'];
    foreach ($filtres as $apiField => $userParam) {
        if (!empty($options[$userParam])) {
            $base["refine.$apiField"] = $options[$userParam];
        }
    }

    $typeFacets = [];
    if (!empty($options['type'])) {
        $type = trim($options['type']);
        $typeMap = [
            "BTS - BTSA - BTSM" => ["BTS - BTSA - BTSM"],
            "Formations des écoles d’ingénieurs" => ["Formations des écoles d’ingénieurs"],
            "Formations du travail social" => ["Formations diplômantes du travail social"],
            "Université" => [
                "Licence","Licence sélective","Licence professionnelle","DEUST",
                "Diplômes d'université ou d'établissement","BUT","DUT",
                "C.M.I - Cursus Master en Ingénierie",
                "I.A.E - Instituts d'administration des entreprises",
                "Formations d'architecture, du paysage et du patrimoine"
            ]
        ];
        if (isset($typeMap[$type])) {
            $typeFacets = $typeMap[$type]; 
        } else {
            $base['q'] = trim(($base['q'] ?? '') . ' ' . $type);
        }
    }

    return ['base' => $base, 'refine_tf' => $typeFacets];
}

/**
 * Récupère la liste des établissements d’enseignement supérieur publics via l’API OpenData.
 *
 * Construit les paramètres à partir de $options (dont les facettes `refine.tf`),
 * appelle l’API, puis retourne la liste des enregistrements (`records`).
 *
 * @param array $options Options de filtre/recherche utilisées pour construire la requête API.
 *
 * @return array Liste des établissements (records) ou un tableau contenant une clé 'error' en cas d’échec.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getEtablissementsSupPublics(array $options = []): array
{
    $built = buildEtablissementsApiParams($options);
    $base  = $built['base'];
    $tfFacets = $built['refine_tf'] ?? [];
    $query = http_build_query($base);
    foreach ($tfFacets as $tf) {
        $query .= '&' . rawurlencode('refine.tf') . '=' . rawurlencode($tf);
    }

    $url  = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?$query";
    $data = callOpenDataApi($url);

    if (isset($data['error'])) return ['error' => $data['error']];
    return $data['records'] ?? ['error' => 'Aucune donnée reçue depuis l’API Parcoursup.'];
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
 * @author  Etudaviz
 * @version 2.0
 */
function callOpenDataApi(string $url): array {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FAILONERROR => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['error' => 'Erreur API HTTP ' . $httpCode];
    }

    $data = json_decode($response, true);

    return is_array($data)
        ? $data
        : ['error' => 'Réponse API invalide'];
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
 * @author  Etudaviz
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
 * @author  Etudaviz
 * @version 2.1 Adaptation au dataset Parcoursup
 */
function formatEtablissement(array $fields, string $recordid = null): array
{
    if (empty($recordid)) {
        return [];
    }
    $rawNom = $fields['fl'] ?? $fields['nm'] ?? 'Nom inconnu';

    $splitters = ['|', ' / ', ' ; ', 'L1 -', 'L2 -', 'L3 -'];
    foreach ($splitters as $s) {
        if (substr_count($rawNom, $s) > 1) {
            $parts = array_filter(array_map('trim', explode($s, $rawNom)));
            $rawNom = reset($parts);
            break;
        }
    }

    if (mb_strlen($rawNom) > 120) {
        $rawNom = mb_substr($rawNom, 0, 120) . '…';
    }

    $nom = trim($rawNom);

    $badge = null;

    $searchZone = strtolower(
        ($fields['aut'] ?? '') . ' ' .         
        ($fields['libelle_long'] ?? '') . ' ' .
        ($fields['fl'] ?? '') . ' ' .
        ($fields['parcours'] ?? '')
    );

    if (preg_match('/\blas\b|acc[eè]s\s*sant[eé]/', $searchZone)) {
        $badge = 'Accès Santé (LAS)';
    }

    if (preg_match('/double\s*dipl[oô]me/', $searchZone)) {
        $badge = 'Double diplôme';
    }

    $type = $fields['tf'] ?? 'Type inconnu';

    $etablissement = $fields['etab_nom'] ?? 'Établissement non précisé';

    $adresseParts = [];
    if (!empty($fields['commune']))     $adresseParts[] = $fields['commune'];
    if (!empty($fields['departement'])) $adresseParts[] = $fields['departement'];
    if (!empty($fields['region']))      $adresseParts[] = $fields['region'];
    $adresse_complete = implode(', ', $adresseParts);

    return [
        'id'             => $recordid,
        'nom'            => $nom,
        'type'           => $type,
        'etablissement'  => $etablissement,
        'adresse'        => $adresse_complete ?: 'Adresse inconnue',
        'badge'          => $badge,

        'ville'          => $fields['commune'] ?? '',
        'departement'    => $fields['departement'] ?? '',
        'region'         => $fields['region'] ?? '',

        'site'           => $fields['etab_url'] ?? '',
        'lien'           => $fields['fiche'] ?? '',

        'coordonnees'    => $fields['etab_gps'] ?? null,

        'annee'          => $fields['annee'] ?? '',
        'code_formation' => $fields['code_formation'] ?? '',
        'apprentissage'  => $fields['app'] ?? '',
        'aut'            => $fields['aut'] ?? '',
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
 * @author  Etudaviz
 * @version 2.0 Migration vers le dataset ONISEP
 */
function getEtablissementById(string $id): ?array {
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query([
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'rows'    => 1,
        'refine.recordid' => $id,
    ]);

    $data = callOpenDataApi($url);
    if (!empty($data['records'][0]['fields'])) {
        $record = $data['records'][0];
        return formatEtablissement($record['fields'], $record['recordid']);
    }
    return null;
}

/**
 * Récupère des informations de débouchés depuis le dataset ONISEP (OpenData Education).
 *
 * La fonction essaie plusieurs stratégies de recherche (par code formation si fourni,
 * puis par libellé exact, puis en plein texte) et retourne les champs utiles si un résultat est trouvé.
 *
 * @param string      $intitule Intitulé de la formation (utilisé pour la recherche).
 * @param string|null $code     Code formation (si disponible) pour une recherche plus précise.
 *
 * @return array|null Tableau contenant 'secteur', 'debouches' et 'poursuite_etudes', ou null si aucun résultat.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getDebouchesDepuisOnisep(string $intitule, ?string $code = null): ?array {
    $base = [
        'dataset' => 'fr-esr-onisep',
        'rows'    => 1,
    ];

    $attempts = [];

    if ($code) {
        $attempts[] = array_merge($base, ['refine.code_formation' => $code]);
    }
    if ($intitule) {
        $attempts[] = array_merge($base, ['refine.fl' => $intitule]);
        $attempts[] = array_merge($base, ['q' => $intitule]);
    }

    foreach ($attempts as $params) {
        $url  = "https://data.education.gouv.fr/api/records/1.0/search?" . http_build_query($params);
        $data = callOpenDataApi($url);
        if (!empty($data['records'][0]['fields'])) {
            $f = $data['records'][0]['fields'];
            return [
                'secteur'          => $f['secteur'] ?? null,
                'debouches'        => $f['debouches'] ?? null,
                'poursuite_etudes' => $f['poursuite_etudes'] ?? null,
            ];
        }
    }
    return null;
}

/**
 * Assure qu'une session PHP est démarrée.
 *
 * Vérifie si une session est déjà active et la démarre si nécessaire,
 * afin de pouvoir utiliser $_SESSION sans erreur.
 *
 * @return void
 *
 * @author  Etudaviz
 * @version 1.0
 */
function ensureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}


/**
 * Enregistre l'utilisateur en session après une connexion réussie.
 *
 * Le tableau $user doit contenir au minimum : id_utilisateur, pseudo, mail.
 *
 * @param array $user Données utilisateur (issues de la BDD).
 *
 * @return void
 *
 * @author  Etudaviz
 * @version 1.0
 */
function loginUser(array $user): void {
    ensureSession();

    $_SESSION['user'] = [
        'id'     => $user['id_utilisateur'],
        'pseudo' => $user['pseudo'],
        'mail'   => $user['mail'],
    ];
}

/**
 * Déconnecte l'utilisateur et supprime les données de session.
 *
 * Vide la session, supprime le cookie de session (si utilisé), puis détruit la session.
 *
 * @return void
 *
 * @author  Etudaviz
 * @version 1.0
 */
function logoutUser(): void {
    ensureSession();

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Retourne les informations de l'utilisateur actuellement connecté.
 *
 * @return array|null Tableau des infos utilisateur (id, pseudo, mail) ou null si personne n'est connecté.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function currentUser(): ?array {
    ensureSession();
    return $_SESSION['user'] ?? null;
}

/**
 * Indique si un utilisateur est connecté.
 *
 * @return bool True si une session utilisateur existe, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function isLoggedIn(): bool {
    return currentUser() !== null;
}

/**
 * Vérifie les identifiants de connexion dans la base de données.
 *
 * $identifiant correspond au pseudo OU à l'email.
 * La fonction compare le mot de passe saisi avec le hash stocké en base.
 *
 * @param string $identifiant Pseudo ou email de l'utilisateur.
 * @param string $password    Mot de passe saisi par l'utilisateur.
 *
 * @return array|null Tableau utilisateur (sans mot de passe) si OK, sinon null.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function verifyLoginDb(string $identifiant, string $password): ?array {
    global $pdo;

    $sql = "SELECT id_utilisateur, pseudo, mail, mot_de_passe, statut_compte
            FROM Utilisateur
            WHERE pseudo = :identifiant
               OR mail   = :identifiant
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['identifiant' => $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return null; 
    }

    if (!password_verify($password, $user['mot_de_passe'])) {
        return null;
    }
    unset($user['mot_de_passe']);
    return $user;
}

/**
 * Envoie un email de vérification (activation de compte) via PHPMailer.
 *
 * @param string $to     Adresse email du destinataire.
 * @param string $pseudo Nom/pseudo du destinataire (pour personnalisation).
 * @param string $link   Lien d’activation à inclure dans le message.
 *
 * @return bool True si l'email a été envoyé, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function sendVerificationMail(string $to, string $pseudo, string $link): bool {
    global $mail_host, $mail_port, $mail_username, $mail_password, $mail_from, $mail_from_name;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $mail_port;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($mail_from, $mail_from_name);
        $mail->addAddress($to, $pseudo);

        $mail->Subject = 'Activation de votre compte Etudaviz';

        $textBody = "Bonjour $pseudo,\n\n"
                  . "Merci de vous être inscrit sur Etudaviz.\n"
                  . "Pour activer votre compte, cliquez sur le lien suivant :\n$link\n\n"
                  . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.\n";

        $htmlBody = "<p>Bonjour <strong>$pseudo</strong>,</p>"
                  . "<p>Merci de vous être inscrit sur Etudaviz.</p>"
                  . "<p>Cliquez sur ce lien pour activer votre compte :</p>"
                  . "<p><a href=\"$link\">Activer mon compte</a></p>"
                  . "<p>Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.</p>";

        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

/**
 * Vérifie un token reCAPTCHA v3 côté serveur.
 *
 * Effectue une requête vers l'API Google, vérifie le succès, l'action attendue
 * et un score minimum (RECAPTCHA_MIN_SCORE). Optionnellement, vérifie le hostname.
 *
 * @param string $token          Token reCAPTCHA envoyé par le client.
 * @param string $expectedAction Action attendue (champ "action" retourné par Google).
 *
 * @return bool True si la vérification est valide, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function verifRecaptchaV3(string $token, string $expectedAction): bool
{
    if ($token === '') return false;

    $postFields = http_build_query([
        'secret'   => RECAPTCHA_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_TIMEOUT        => 5,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return false;

    $json = json_decode($response, true);
    if (!is_array($json) || empty($json['success'])) return false;

    if (($json['action'] ?? '') !== $expectedAction) return false;
    if (($json['score'] ?? 0) < RECAPTCHA_MIN_SCORE) return false;

    if (defined('RECAPTCHA_ALLOWED_HOSTS') && !empty($json['hostname'])) {
        if (!in_array($json['hostname'], RECAPTCHA_ALLOWED_HOSTS, true)) return false;
    }

    return true;
}

/**
 * Recherche des établissements (enseignement sup public) via l’API OpenData.
 *
 * Appelle `getEtablissementsSupPublics()` avec une requête texte et une limite,
 * puis formate chaque résultat via `formatEtablissement()`.
 *
 * @param string $query Texte de recherche.
 *
 * @return array Liste des établissements formatés (tableau vide si erreur ou aucun résultat).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function searchEtablissements(string $query): array {
    $raw = getEtablissementsSupPublics([
        "search" => $query,
        "limit" => 12
    ]);
    if (empty($raw) || isset($raw["error"])) {
        return [];
    }
    $results = [];
    foreach ($raw as $record) {
        $results[] = formatEtablissement($record["fields"], $record["recordid"]);
    }
    return $results;
}

/**
 * Vérifie le statut d’un compte et renvoie un message adapté au contexte.
 *
 * Retourne `null` si le compte est "actif", sinon un message explicatif pour l’utilisateur.
 *
 * @param string|null $statut  Statut du compte (ex: actif, inactif, suspendu).
 * @param string      $context Contexte d’utilisation (ex: 'login', 'reset_password').
 *
 * @return string|null Message à afficher si statut bloquant, sinon null.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function checkStatutCompte(?string $statut, string $context = 'login'): ?string
{
    $statut = strtolower(trim((string)$statut));

    return match ($statut) {
        'actif' => null,

        'inactif' => ($context === 'reset_password')
            ? "Votre compte n'est pas activé. Activez d'abord votre compte via l'email reçu, puis réessayez."
            : "Votre compte n'est pas encore activé. Merci de cliquer sur le lien d'activation reçu par email.",

        'suspendu' =>
            "Votre compte est suspendu car vous n’avez pas respecté nos règles. "
            . "Si vous pensez qu’il s’agit d’une erreur, contactez l’administration via le formulaire de contact du site.",

        default =>
            "Statut de compte inconnu. Contactez l'administration via le formulaire de contact du site.",
    };
}


/**
 * Détecte si un pseudo contient un mot interdit (liste dans un fichier texte).
 *
 * Le fichier peut contenir des lignes vides et des commentaires (lignes commençant par #).
 * La vérification est faite en insensible à la casse (UTF-8) et recherche un mot interdit
 * comme sous-chaîne dans le pseudo.
 *
 * @param string $pseudo   Pseudo à vérifier.
 * @param string $pathTxt  Chemin vers le fichier de mots interdits.
 *
 * @return bool True si le pseudo contient un mot interdit, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function forbiddenMotPseudo(string $pseudo, string $pathTxt): bool
{
    if (!is_readable($pathTxt)) return false;

    $liste = file($pathTxt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($liste === false) return false;

    $p = mb_strtolower(trim($pseudo), 'UTF-8');
    if ($p === '') return false;

    foreach ($liste as $mot) {
        $mot = trim($mot);
        if ($mot === '' || str_starts_with($mot, '#')) continue;

        if (mb_stripos($p, mb_strtolower($mot, 'UTF-8'), 0, 'UTF-8') !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Valide les champs d'inscription.
 *
 * Vérifie : champs remplis, format pseudo, pseudo autorisé (mots interdits),
 * email valide, cohérence des mots de passe et règles de complexité.
 *
 * @param string $pseudo    Pseudo saisi.
 * @param string $mail      Email saisi.
 * @param string $password  Mot de passe.
 * @param string $password2 Confirmation du mot de passe.
 *
 * @return string|null Message d’erreur si invalide, sinon null.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function validateRegistrationInput(
    string $pseudo,
    string $mail,
    string $password,
    string $password2
): ?string {

    if ($pseudo === '' || $mail === '' || $password === '' || $password2 === '') {
        return "Veuillez remplir tous les champs.";
    }

    if (!preg_match('/^[A-Za-z]{2,12}$/', $pseudo)) {
        return "Le pseudo doit contenir uniquement des lettres et faire entre 2 et 12 caractères.";
    }

    $pathTxt = dirname(__DIR__) . '/data/list-mots-interdits.txt';
    if (forbiddenMotPseudo($pseudo, $pathTxt)) {
        return "Ce pseudo n'est pas autorisé.";
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return "Adresse mail invalide.";
    }

    if ($password !== $password2) {
        return "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 8) {
        return "Le mot de passe doit faire au moins 8 caractères.";
    }

    if (!preg_match('/[A-Za-z]/', $password)) {
        return "Le mot de passe doit contenir au moins une lettre.";
    }

    if (!preg_match('/[\*\/\-]/', $password)) {
        return "Le mot de passe doit contenir au moins un caractère spécial parmi * / -.";
    }

    if (!preg_match('/[0-9]/', $password)) {
    return "Le mot de passe doit contenir au moins un chiffre.";
    }

    return null;
}

/**
 * Vérifie si un pseudo ou un email est déjà utilisé en base de données.
 *
 * @param PDO    $pdo    Connexion PDO.
 * @param string $pseudo Pseudo à tester.
 * @param string $mail   Email à tester.
 *
 * @return bool True si le pseudo ou l’email existe déjà, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function isPseudoOrMailUsed(PDO $pdo, string $pseudo, string $mail): bool {
    $sql = "SELECT COUNT(*) AS nb 
            FROM Utilisateur 
            WHERE pseudo = :pseudo OR mail = :mail";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'pseudo' => $pseudo,
        'mail'   => $mail,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row && (int)$row['nb'] > 0;
}

/**
 * Crée un utilisateur en base et retourne son identifiant.
 *
 * Le mot de passe est hashé via `password_hash()` et le compte est créé avec
 * le statut "inactif" (activation par email).
 *
 * @param PDO    $pdo      Connexion PDO.
 * @param string $pseudo   Pseudo de l'utilisateur.
 * @param string $mail     Email de l'utilisateur.
 * @param string $password Mot de passe en clair (sera hashé).
 *
 * @return int|null id_utilisateur si succès, sinon null.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function createUser(PDO $pdo, string $pseudo, string $mail, string $password): ?int {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Utilisateur (pseudo, mail, mot_de_passe, date_inscription, statut_compte)
            VALUES (:pseudo, :mail, :mot_de_passe, NOW(), 'inactif')";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        'pseudo'       => $pseudo,
        'mail'         => $mail,
        'mot_de_passe' => $hash,
    ]);

    if (!$ok) {
        return null;
    }

    return (int)$pdo->lastInsertId();
}

/**
 * Construit une clé unique pour identifier une formation (code + UAI).
 *
 * @param array $f Tableau contenant au minimum 'code_formation' et/ou 'etab_uai'.
 *
 * @return string Clé unique au format "code-uai".
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getFormationUniqueKey(array $f): string {
    $code = $f['code_formation'] ?? '';
    $uai  = $f['etab_uai'] ?? '';
    return $code . '-' . $uai;
}

/**
 * Fusionne deux enregistrements de formation et conserve le plus récent.
 *
 * Compare l'année (`annee`) et garde le record ayant l'année la plus élevée.
 * Fusionne ensuite certains champs à listes (ex: 'amg') sans doublons.
 *
 * @param array $old Enregistrement existant.
 * @param array $new Nouvel enregistrement.
 *
 * @return array Enregistrement fusionné.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function mergeFormationRecords(array $old, array $new): array {
    $oldYear = intval($old['annee'] ?? 0);
    $newYear = intval($new['annee'] ?? 0);

    $keep = $newYear >= $oldYear ? $new : $old;
    $other = $newYear >= $oldYear ? $old : $new;

    if (!empty($other['amg'])) {
        $keep['amg'] = implode('|', array_unique(array_filter(array_merge(
            explode('|', $keep['amg'] ?? ''),
            explode('|', $other['amg'] ?? '')
        ))));
    }

    return $keep;
}

/**
 * Recherche des métiers dans l’API ESCO (type occupation) et retourne des infos simplifiées.
 *
 * Fait une recherche texte, puis appelle l’API de détail pour récupérer le code ISCO
 * et jusqu’à 2 compétences essentielles.
 *
 * @param string $query  Texte de recherche (min 2 caractères).
 * @param int    $limit  Nombre max de résultats.
 * @param int    $offset Décalage (pagination).
 *
 * @return array Liste de métiers (title, uri, isco, essentialSkills).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function escoSearch(string $query, int $limit = 4, int $offset = 0): array {
    if (strlen($query) < 2) return [];

    $url = "https://ec.europa.eu/esco/api/search?"
         . "text=" . urlencode($query)
         . "&type=occupation"
         . "&language=fr"
         . "&limit=" . intval($limit)
         . "&offset=" . intval($offset);

    $json = @file_get_contents($url);
    if (!$json) return [];

    $data = json_decode($json, true);
    if (empty($data["_embedded"]["results"])) return [];

    $results = [];

    foreach ($data["_embedded"]["results"] as $item) {
        if (empty($item["uri"]) || empty($item["title"])) continue;

        $uri = $item["uri"];

        $detailUrl  = "https://ec.europa.eu/esco/api/resource/occupation?uri="
                    . urlencode($uri) . "&language=fr";
        $detailJson = @file_get_contents($detailUrl);
        if (!$detailJson) continue;

        $detail = json_decode($detailJson, true);

        $isco = $detail["code"] ?? "";

        $skills = [];
        if (!empty($detail["_links"]["hasEssentialSkill"])) {
            foreach ($detail["_links"]["hasEssentialSkill"] as $s) {
                if (!empty($s["title"])) {
                    $skills[] = $s["title"];
                }
            }
        }
        $skills = array_slice($skills, 0, 2);

        $results[] = [
            "title"           => $item["title"],
            "uri"             => $uri,
            "isco"            => $isco,
            "essentialSkills" => $skills
        ];
    }

    return $results;
}

/**
 * Traduit un texte anglais vers le français via l’API LibreTranslate.
 *
 * En cas d’échec de l’API, renvoie le texte original (fallback).
 *
 * @param string $text Texte à traduire (supposé en anglais).
 *
 * @return string Texte traduit en français (ou texte d'origine si erreur).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function escoTranslateToFrench(string $text): string {
    $url = "https://libretranslate.de/translate";

    $payload = [
        "q" => $text,
        "source" => "en",
        "target" => "fr",
        "format" => "text"
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => json_encode($payload)
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) return $text; 

    $json = json_decode($result, true);
    return $json["translatedText"] ?? $text;
}

/**
 * Récupère le détail d’un métier depuis l’API ESCO à partir de son URI.
 *
 * Appelle l’endpoint ESCO (occupation), extrait les champs utiles (titre, code ISCO,
 * description, synonymes, compétences essentielles/optionnelles) et applique des fallbacks
 * si certaines données sont absentes.
 *
 * @param string $uri URI ESCO du métier.
 *
 * @return array|null Données du métier (title, isco, description, altLabels, skillsEssential, skillsOptional)
 *                    ou null si l’API ne répond pas / JSON invalide.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function escoGetMetier(string $uri): ?array {

    $url = "https://ec.europa.eu/esco/api/resource/occupation?uri=" 
            . urlencode($uri) . "&language=fr";

    $json = @file_get_contents($url);
    if (!$json) return null;

    $data = json_decode($json, true);
    if (!$data) return null;
    $extractText = function($value) {

        
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            if (isset($value["literal"])) {
                return $value["literal"];
            }
            return implode(" ", array_filter($value, fn($v) => is_string($v)));
        }

        return "";
    };
    $cleanDescription = function($txt) {
        if (!is_string($txt)) return "";
        $txt = trim(strip_tags($txt));
        $invalid = ["plain/text", "text/plain", "literal", "string", "null"];

        if (in_array(strtolower($txt), $invalid)) {
            return "";
        }
        if (strlen($txt) < 20) {
            return "";
        }
        return $txt;
    };
    $isco = $data["code"] ?? "";
    $description = "";
    if (isset($data["description"]["fr"])) {
        $raw = $extractText($data["description"]["fr"]);
        $description = $cleanDescription($raw);
    }
    if (empty($description) && isset($data["description"]["en"])) {
        $rawEN = $extractText($data["description"]["en"]);
        $cleanEN = $cleanDescription($rawEN);

        if (!empty($cleanEN)) {
            $description = escoTranslateToFrench($cleanEN);
        }
    }
    if (empty($description)) {
        $description = "Ce métier est associé au code ISCO {$isco} et regroupe différentes activités professionnelles nécessitant des compétences spécialisées.";
    }
    $alt = [];
    if (!empty($data["alternativeLabel"])) {
        foreach ($data["alternativeLabel"] as $lbl) {
            if (!empty($lbl["fr"])) {
                $alt[] = $lbl["fr"];
            }
        }
    }
    $skillsEssential = [];
    if (!empty($data["_links"]["hasEssentialSkill"])) {
        foreach ($data["_links"]["hasEssentialSkill"] as $s) {
            if (!empty($s["title"])) {
                $skillsEssential[] = $s["title"];
            }
        }
    }
    $skillsEssential = array_slice($skillsEssential, 0, 3);
    $skillsOptional = [];
    if (!empty($data["_links"]["hasOptionalSkill"])) {
        foreach ($data["_links"]["hasOptionalSkill"] as $s) {
            if (!empty($s["title"])) {
                $skillsOptional[] = $s["title"];
            }
        }
    }
    $skillsOptional = array_slice($skillsOptional, 0, 3);
    return [
        "title"           => $data["preferredLabel"]["fr"] ?? "Métier",
        "isco"            => $isco,
        "description"     => $description,
        "altLabels"       => $alt,
        "skillsEssential" => $skillsEssential,
        "skillsOptional"  => $skillsOptional
    ];
}

/**
 * Nettoie une description (suppression tags + valeurs parasites + longueur minimale).
 *
 * @param mixed $txt Texte à nettoyer.
 *
 * @return string Description nettoyée (ou chaîne vide si invalide).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function cleanDescription($txt) {
    if (!is_string($txt)) return "";

    $txt = trim($txt);

    $invalid = ["plain/text", "text/plain", "plain", "string", "null"];

    if (in_array(strtolower($txt), $invalid)) {
        return "";
    }
    if (strlen($txt) < 20) {
        return "";
    }

    return strip_tags($txt);
}

/**
 * Génère un token d’activation de compte et le stocke en base pour un utilisateur donné.
 *
 * @param PDO $pdo          Connexion PDO.
 * @param int $idUtilisateur ID de l’utilisateur.
 *
 * @return string|null Token généré (64 caractères hex) ou null en cas d’erreur.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function createActivationToken(PDO $pdo, int $idUtilisateur): ?string {
    $token = bin2hex(random_bytes(32));

    $sql = "UPDATE Utilisateur
            SET token_activation = :token
            WHERE id_utilisateur = :id";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        'token' => $token,
        'id'    => $idUtilisateur,
    ]);

    if (!$ok) {
        return null;
    }

    return $token;
}

/**
 * Envoie un mail de contact à l’adresse du site via PHPMailer.
 *
 * Le message est envoyé à l’adresse du site ($mail_from) et le champ Reply-To est défini
 * avec l’email de l’utilisateur pour pouvoir lui répondre directement.
 *
 * @param string $fromMail    Email de l’expéditeur (utilisateur).
 * @param string $fromName    Nom/pseudo de l’expéditeur.
 * @param string $subject     Sujet saisi.
 * @param string $messageText Message saisi.
 *
 * @return bool True si envoyé, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function sendContactMail(string $fromMail, string $fromName, string $subject, string $messageText): bool {
    global $mail_host, $mail_port, $mail_username, $mail_password, $mail_from, $mail_from_name;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $mail_port;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($mail_from, $mail_from_name);
        $mail->addAddress($mail_from, $mail_from_name); 

        $mail->addReplyTo($fromMail, $fromName);

        $mail->Subject = 'Contact Etudaviz : ' . $subject;

        $textBody = "Message envoyé depuis le formulaire de contact Etudaviz.\n\n"
                  . "De : $fromName <$fromMail>\n"
                  . "Sujet : $subject\n\n"
                  . "Message :\n$messageText\n";

        $htmlBody = "<p><strong>Nouveau message depuis le formulaire de contact Etudaviz</strong></p>"
                  . "<p><strong>De :</strong> " . htmlspecialchars($fromName) . " &lt;" . htmlspecialchars($fromMail) . "&gt;</p>"
                  . "<p><strong>Sujet :</strong> " . htmlspecialchars($subject) . "</p>"
                  . "<p><strong>Message :</strong><br>" . nl2br(htmlspecialchars($messageText)) . "</p>";

        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

/**
 * Récupère les avis actifs d’une formation (avec le pseudo de l’auteur).
 *
 * @param mixed $id_formation Identifiant de la formation.
 *
 * @return array Liste des avis (triés du plus récent au plus ancien).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getAvisByFormationId($id_formation) {
    global $pdo;

    $sql = "
    SELECT a.*, u.pseudo 
    FROM Avis a
    JOIN Utilisateur u ON a.id_utilisateur = u.id_utilisateur
    WHERE a.id_formation = ? AND a.statut = 'actif'
    ORDER BY a.date_publication DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_formation]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les derniers avis actifs (avec le pseudo de l’auteur).
 *
 * @param int $limit Nombre maximum d’avis à retourner.
 *
 * @return array Liste des avis (triés du plus récent au plus ancien).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getLatestAvis(int $limit = 10): array {
    global $pdo;

    $sql = "
        SELECT a.*, u.pseudo 
        FROM Avis a
        JOIN Utilisateur u ON a.id_utilisateur = u.id_utilisateur
        WHERE statut = 'actif'
        ORDER BY date_publication DESC
        LIMIT ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère tous les avis actifs, avec quelques champs sélectionnés et le pseudo de l’auteur.
 *
 * @return array Liste des avis (triés du plus récent au plus ancien).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getAvisParFormation(): array {
    global $pdo;
    $sql = "
        SELECT 
            a.id_avis,
            a.id_formation,
            a.titre_avis,
            a.description,
            a.date_publication,
            a.date_experience,
            a.likes,
            u.pseudo AS auteur
        FROM Avis a
        INNER JOIN Utilisateur u ON a.id_utilisateur = u.id_utilisateur
        WHERE a.statut = 'actif'
        ORDER BY a.date_publication DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les avis d’un utilisateur.
 *
 * @param int $id_utilisateur ID de l’utilisateur.
 *
 * @return array Liste des avis de l’utilisateur (triés du plus récent au plus ancien).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getAvisByUser(int $id_utilisateur): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM Avis
        WHERE id_utilisateur = ?
        ORDER BY date_publication DESC
    ");

    $stmt->execute([$id_utilisateur]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retourne le pseudo de l’utilisateur connecté (depuis la session).
 *
 * @return string Pseudo si connecté, sinon chaîne vide.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getPseudo() {
    if (isset($_SESSION['user']['pseudo'])) {
        return $_SESSION['user']['pseudo'];
    }
    return '';
}

/**
 * Recherche un utilisateur en base à partir de son email.
 *
 * @param PDO    $pdo  Connexion PDO.
 * @param string $mail Email à rechercher.
 *
 * @return array|null Infos utilisateur (id_utilisateur, pseudo, mail, statut_compte) ou null si introuvable.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function findUserByEmail(PDO $pdo, string $mail): ?array
{
    $sql = "SELECT id_utilisateur, pseudo, mail, statut_compte
            FROM Utilisateur
            WHERE mail = :mail
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['mail' => $mail]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    return $u ?: null;
}

/**
 * Génère un token de réinitialisation de mot de passe et le stocke en base.
 *
 * @param PDO $pdo           Connexion PDO.
 * @param int $idUtilisateur ID de l’utilisateur.
 *
 * @return string|null Token généré (64 hex) ou null en cas d’échec.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function createPasswordResetToken(PDO $pdo, int $idUtilisateur): ?string
{
    $token = bin2hex(random_bytes(32));

    $sql = "UPDATE Utilisateur
            SET token_activation = :token
            WHERE id_utilisateur = :id";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        'token' => $token,
        'id'    => $idUtilisateur,
    ]);

    return $ok ? $token : null;
}

/**
 * Envoie un email de réinitialisation de mot de passe via PHPMailer.
 *
 * @param string $to     Adresse email du destinataire.
 * @param string $pseudo Pseudo du destinataire.
 * @param string $link   Lien de réinitialisation à inclure.
 *
 * @return bool True si envoyé, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function sendPasswordResetMail(string $to, string $pseudo, string $link): bool
{
    global $mail_host, $mail_port, $mail_username, $mail_password, $mail_from, $mail_from_name;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $mail_port;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($mail_from, $mail_from_name);
        $mail->addAddress($to, $pseudo);

        $mail->Subject = 'Réinitialisation de votre mot de passe Etudaviz';

        $textBody = "Bonjour $pseudo,\n\n"
                  . "Vous avez demandé à réinitialiser votre mot de passe.\n"
                  . "Cliquez sur le lien suivant :\n$link\n\n"
                  . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.\n";

        $htmlBody = "<p>Bonjour <strong>$pseudo</strong>,</p>"
                  . "<p>Vous avez demandé à réinitialiser votre mot de passe.</p>"
                  . "<p><a href=\"$link\">Réinitialiser mon mot de passe</a></p>"
                  . "<p>Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.</p>";

        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

/**
 * Recherche un utilisateur à partir d’un token (activation / reset).
 *
 * @param PDO    $pdo   Connexion PDO.
 * @param string $token Token à vérifier.
 *
 * @return array|null Infos utilisateur si trouvé, sinon null.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function findUserByToken(PDO $pdo, string $token): ?array
{
    $sql = "SELECT id_utilisateur, pseudo, mail, statut_compte
            FROM Utilisateur
            WHERE token_activation = :token
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    return $u ?: null;
}

/**
 * Réinitialise le mot de passe d’un utilisateur à partir d’un token valide.
 *
 * Met à jour le hash du mot de passe et invalide le token (mis à NULL).
 *
 * @param PDO    $pdo          Connexion PDO.
 * @param int    $idUtilisateur ID de l’utilisateur.
 * @param string $token        Token de réinitialisation.
 * @param string $newPassword  Nouveau mot de passe (en clair, sera hashé).
 *
 * @return bool True si la mise à jour a bien été effectuée, sinon false.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function resetPasswordWithToken(PDO $pdo, int $idUtilisateur, string $token, string $newPassword): bool
{
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE Utilisateur
            SET mot_de_passe = :hash,
                token_activation = NULL
            WHERE id_utilisateur = :id
              AND token_activation = :token";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'hash'  => $hash,
        'id'    => $idUtilisateur,
        'token' => $token,
    ]);

    return $stmt->rowCount() === 1;
}

/**
 * Met à jour les statistiques de connexion d’un utilisateur.
 *
 * Met à jour la date de dernière connexion (NOW()) et incrémente le compteur de connexions.
 *
 * @param PDO $pdo     Connexion PDO.
 * @param int $userId  ID de l’utilisateur connecté.
 *
 * @return void
 *
 * @author  Etudaviz
 * @version 1.0
 */
function updateUserLoginStats(PDO $pdo, int $userId): void {
    $stmt = $pdo->prepare("
        UPDATE Utilisateur 
        SET last_login = NOW(), login_count = login_count + 1
        WHERE id_utilisateur = ?
    ");
    $stmt->execute([$userId]);
}

/**
 * Récupère les activités récentes d’un utilisateur (avis, etc.).
 *
 * Retourne une liste d’actions triées de la plus récente à la plus ancienne.
 *
 * @param PDO $pdo     Connexion PDO.
 * @param int $userId  ID de l’utilisateur.
 * @param int $limit   Nombre d’activités à retourner.
 *
 * @return array Liste des activités (type, description, date_action).
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getRecentActivities(PDO $pdo, int $userId, int $limit = 5): array {
    $limit = (int) $limit; 

    $sql = "
        SELECT 'avis' AS type, titre_avis AS description, date_publication AS date_action
        FROM Avis
        WHERE id_utilisateur = ?
        ORDER BY date_action DESC
        LIMIT $limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Calcule la note moyenne sur 10 pour un avis donné.
 *
 * La moyenne est calculée à partir des notes des critères (table Note_Critere),
 * puis convertie sur 10 (x2) et arrondie à 1 décimale.
 *
 * @param PDO $pdo    Connexion PDO.
 * @param int $idAvis ID de l’avis.
 *
 * @return float|null Moyenne sur 10, ou null si aucune note.
 *
 * @author  Etudaviz
 * @version 1.0
 */
function getMoyenneAvisSur10(PDO $pdo, int $idAvis): ?float
{
    $stmt = $pdo->prepare("SELECT valeur FROM Note_Critere WHERE id_avis = ?");
    $stmt->execute([$idAvis]);
    $notes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($notes)) {
        $moyenne10 = round((array_sum($notes) / count($notes)) * 2, 1);
        return $moyenne10;
    }

    return null; 
}

?>
