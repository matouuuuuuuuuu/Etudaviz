<?php
include __DIR__ . "/../../config/bdconnect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

function renderMetierCard(array $m): string
{
    $html = '<div class="etab-card">';

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





function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=etudaviz;charset=utf8", "root", ""); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // ⚠️ Si la base n'est pas accessible, on renvoie null
        return null;
    }
}

/**
 * Retourne le taux de satisfaction (%) des utilisateurs
 * Si la BDD n’est pas disponible, renvoie NULL
 */
function getTauxSatisfaction() {
    $pdo = getDBConnection();
    if (!$pdo) return null; // 🧩 fallback si pas de BDD

    try {
        $query = $pdo->query("SELECT AVG(satisfaction) AS taux FROM avis");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return round($result['taux'], 1);
    } catch (Exception $e) {
        return null; // 🔒 sécurité supplémentaire
    }
}

/**
 * Retourne le nombre d’avis utilisateurs
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
 * Retourne le nombre de partenaires institutionnels
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
 */
function buildEtablissementsApiParams(array $options = []): array
{
    $base = [
        'dataset' => 'fr-esr-cartographie_formations_parcoursup',
        'rows'    => $options['limit'] ?? 100,
        'facet'   => ['region', 'departement', 'commune', 'tf'],
    ];

    if (!empty($options['offset'])) $base['start'] = (int)$options['offset'];
    if (!empty($options['search'])) $base['q'] = $options['search'];

    $filtres = ['region' => 'region', 'departement' => 'departement', 'commune' => 'ville'];
    foreach ($filtres as $apiField => $userParam) {
        if (!empty($options[$userParam])) {
            $base["refine.$apiField"] = $options[$userParam];
        }
    }

    // Gestion "type"
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
    // Nom de la formation
    $nom = $fields['fl']
        ?? $fields['nm']
        ?? 'Nom inconnu';

    // Type (Licence, BUT, BTS…)
    $type = $fields['tf']
        ?? 'Type inconnu';

    // Nom de l’établissement
    $etablissement = $fields['etab_nom']
        ?? 'Établissement non précisé';

    // Adresse complète
    $adresseParts = [];
    if (!empty($fields['commune']))     $adresseParts[] = $fields['commune'];
    if (!empty($fields['departement'])) $adresseParts[] = $fields['departement'];
    if (!empty($fields['region']))      $adresseParts[] = $fields['region'];
    $adresse_complete = implode(', ', $adresseParts);

    return [
        // ⭐ IMPORTANT : on garde recordid pour que fiche_formation.php continue de marcher
        'id'             => $recordid ?? uniqid('formation_'),

        // Champs principaux
        'nom'            => $nom,
        'type'           => $type,
        'etablissement'  => $etablissement,
        'adresse'        => $adresse_complete ?: 'Adresse inconnue',

        // Localisation
        'ville'          => $fields['commune'] ?? '',
        'departement'    => $fields['departement'] ?? '',
        'region'         => $fields['region'] ?? '',

        // Liens
        'site'           => $fields['etab_url'] ?? '',
        'lien'           => $fields['fiche'] ?? '',

        // Coordonnées GPS
        'coordonnees'    => $fields['etab_gps'] ?? null,

        // Métadonnées utiles
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
 * @author  Étudaviz
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


function getDebouchesDepuisOnisep(string $intitule, ?string $code = null): ?array {
    // 1) si on a un code formation (ou un libellé exact), tenter refine exact
    $base = [
        'dataset' => 'fr-esr-onisep',
        'rows'    => 1,
    ];

    $attempts = [];

    if ($code) {
        $attempts[] = array_merge($base, ['refine.code_formation' => $code]);
    }
    if ($intitule) {
        // tentative refine exact sur libellé si le dataset le prévoit
        $attempts[] = array_merge($base, ['refine.fl' => $intitule]);
        // fallback plein texte
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
                // tu peux aussi exposer 'competences' si dispo : $f['competences'] ?? null,
            ];
        }
    }
    return null;
}



function getFranceTravailAccessToken() {
    $clientId = "PAR_malbrunalwaysdatanet_de07e6739e2412366eaa75b683e3ebf844107c6173c733fd44b9d0822420edef";
    $clientSecret = "d9d31cae50ebca6fa275c3aaa543b9bbfbc74a910cd123e27f03bf6e6e78b13a";

    $url = "https://entreprise.francetravail.fr/connexion/oauth2/access_token?realm=/partenaire";

    $postFields = http_build_query([
        "grant_type" => "client_credentials",
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "scope" => "api_rome-metiersv1"
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}



function ensureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}


/**
 * Enregistre l'utilisateur en session après une connexion réussie.
 * $user doit contenir au moins id_utilisateur, pseudo, mail.
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
 * Supprime les infos de session de l'utilisateur.
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
 * Retourne les infos de l'utilisateur connecté, ou null si personne.
 */
function currentUser(): ?array {
    ensureSession();
    return $_SESSION['user'] ?? null;
}

/**
 * True si quelqu’un est connecté.
 */
function isLoggedIn(): bool {
    return currentUser() !== null;
}

/**
 * Vérifie le login en base de données.
 * $identifiant = pseudo OU mail
 * $password = mot de passe tapé par l'utilisateur
 *
 * Retourne le tableau utilisateur (sans le mot de passe) si OK, sinon null.
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


function captchaInit(): void {
    ensureSession();
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_result']   = $a + $b;
    $_SESSION['captcha_question'] = "$a + $b";
}

function captchaQuestion(): string {
    ensureSession();
    return $_SESSION['captcha_question'] ?? "2 + 2";
}

function captchaCheck(string $answer): bool {
    ensureSession();
    if (!isset($_SESSION['captcha_result'])) {
        return false;
    }
    return (int)$answer === (int)$_SESSION['captcha_result'];
}

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
 * Valide les champs d'inscription.
 * Retourne un message d'erreur ou null si tout est OK.
 */
function validateRegistrationInput(
    string $pseudo,
    string $mail,
    string $password,
    string $password2,
    string $captchaAnswer
): ?string {
    // Champs vides
    if ($pseudo === '' || $mail === '' || $password === '' || $password2 === '' || $captchaAnswer === '') {
        return "Veuillez remplir tous les champs.";
    }

    // Pseudo : uniquement des lettres, longueur 2 à 12
    if (!preg_match('/^[A-Za-z]{2,12}$/', $pseudo)) {
        return "Le pseudo doit contenir uniquement des lettres et faire entre 2 et 12 caractères.";
    }

    // Mail valide
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return "Adresse mail invalide.";
    }

    // Mots de passe identiques
    if ($password !== $password2) {
        return "Les mots de passe ne correspondent pas.";
    }

    // Captcha
    if (!captchaCheck($captchaAnswer)) {
        return "Captcha incorrect.";
    }

    return null; 
}

/**
 * Vérifie si le pseudo ou l’email est déjà utilisé en base.
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
 * Crée un utilisateur en base et retourne son id_utilisateur,
 * ou null en cas d'erreur.
 */
function createUser(PDO $pdo, string $pseudo, string $mail, string $password): ?int {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Le compte est créé comme "inactif"
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

function getFormationUniqueKey(array $f): string {
    $code = $f['code_formation'] ?? '';
    $uai  = $f['etab_uai'] ?? '';
    return $code . '-' . $uai;
}

function mergeFormationRecords(array $old, array $new): array {

    // Comparer les années
    $oldYear = intval($old['annee'] ?? 0);
    $newYear = intval($new['annee'] ?? 0);

    $keep = $newYear >= $oldYear ? $new : $old;
    $other = $newYear >= $oldYear ? $old : $new;

    // Fusion des champs à liste
    if (!empty($other['amg'])) {
        $keep['amg'] = implode('|', array_unique(array_filter(array_merge(
            explode('|', $keep['amg'] ?? ''),
            explode('|', $other['amg'] ?? '')
        ))));
    }

    return $keep;
}



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


function escoTranslateToFrench(string $text): string {
    // ⭐ Simple trado automatique avec l’API LibreTranslate (gratuite)
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

    if ($result === false) return $text; // fallback EN si API HS

    $json = json_decode($result, true);
    return $json["translatedText"] ?? $text;
}

function escoGetMetier(string $uri): ?array {

    $url = "https://ec.europa.eu/esco/api/resource/occupation?uri=" 
            . urlencode($uri) . "&language=fr";

    $json = @file_get_contents($url);
    if (!$json) return null;

    $data = json_decode($json, true);
    if (!$data) return null;


    /* ---------------------------------------------------------
        Extract raw text from ESCO structure
    --------------------------------------------------------- */
    $extractText = function($value) {

        // Cas normal : string
        if (is_string($value)) {
            return $value;
        }

        // Cas tableau : peut contenir "literal"
        if (is_array($value)) {
            if (isset($value["literal"])) {
                return $value["literal"];
            }

            // parfois tableau indexé → texte dans [0]
            return implode(" ", array_filter($value, fn($v) => is_string($v)));
        }

        return "";
    };


    /* ---------------------------------------------------------
        Clean description helper
    --------------------------------------------------------- */
    $cleanDescription = function($txt) {

        if (!is_string($txt)) return "";

        $txt = trim(strip_tags($txt));

        // valeurs invalides fréquemment renvoyées
        $invalid = ["plain/text", "text/plain", "literal", "string", "null"];

        if (in_array(strtolower($txt), $invalid)) {
            return "";
        }

        // éviter les faux textes
        if (strlen($txt) < 20) {
            return "";
        }

        return $txt;
    };


    /* ---------------------------------------------------------
        ISCO
    --------------------------------------------------------- */
    $isco = $data["code"] ?? "";


    /* ---------------------------------------------------------
        DESCRIPTION
    --------------------------------------------------------- */
    $description = "";

    // 1) DESCRIPTION FR
    if (isset($data["description"]["fr"])) {
        $raw = $extractText($data["description"]["fr"]);
        $description = $cleanDescription($raw);
    }

    // 2) Fallback EN
    if (empty($description) && isset($data["description"]["en"])) {
        $rawEN = $extractText($data["description"]["en"]);
        $cleanEN = $cleanDescription($rawEN);

        if (!empty($cleanEN)) {
            $description = escoTranslateToFrench($cleanEN);
        }
    }

    // 3) Fallback final
    if (empty($description)) {
        $description = "Ce métier est associé au code ISCO {$isco} et regroupe différentes activités professionnelles nécessitant des compétences spécialisées.";
    }


    /* ---------------------------------------------------------
        SYNONYMES
    --------------------------------------------------------- */
    $alt = [];
    if (!empty($data["alternativeLabel"])) {
        foreach ($data["alternativeLabel"] as $lbl) {
            if (!empty($lbl["fr"])) {
                $alt[] = $lbl["fr"];
            }
        }
    }


    /* ---------------------------------------------------------
        COMPÉTENCES
    --------------------------------------------------------- */
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


function cleanDescription($txt) {
    if (!is_string($txt)) return "";

    $txt = trim($txt);

    // Supprimer les valeurs parasites type "plain/text"
    $invalid = ["plain/text", "text/plain", "plain", "string", "null"];

    if (in_array(strtolower($txt), $invalid)) {
        return "";
    }

    // Si la description est trop courte → non pertinente
    if (strlen($txt) < 20) {
        return "";
    }

    return strip_tags($txt);
}








/**
 * Génère un token d'activation (de compte) et le stocke en base pour l'utilisateur donné.
 * Retourne le token ou null en cas d'erreur.
 */
function createActivationToken(PDO $pdo, int $idUtilisateur): ?string {
    // 64 caractères hexadécimaux
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
 * Envoie un mail de contact à l'adresse du site.
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







?>
