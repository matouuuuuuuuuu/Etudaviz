<?php
// Incrémentation du compteur
function incrementCounter(): int {
    $file = __DIR__ . '/counter.txt';
    if (!file_exists($file)) file_put_contents($file, '0');

    $visites = (int)file_get_contents($file);
    $visites++;
    file_put_contents($file, $visites);
    return $visites;
}


// Fonction pour récupérer la date du jour
function getCurrentDate(string $format = "d/m/Y"): string {
    return date($format);
}

function buildEtablissementsApiParams(array $options = []): array {
    $params = [
        'dataset' => 'fr-esr-implantations_etablissements_d_enseignement_superieur_publics',
        'rows' => $options['limit'] ?? 100,
        'facet' => [
            'reg_nom',
            'dep_nom',
            'com_nom',
            'services',
            'type_d_etablissement',
            'type_uai',
            'bcnag_n_nature_uai_libelle_editi'
        ]
    ];
    if (!empty($options['search'])) {
        $params['q'] = $options['search'];
    }
    if (!empty($options['offset'])) {
        $params['start'] = (int)$options['offset'];
    }
    $filtres = [
        'reg_nom' => 'region',
        'dep_nom' => 'departement',
        'com_nom' => 'ville',
        'type_d_etablissement' => 'type'
    ];
    foreach ($filtres as $apiField => $userParam) {
        if (!empty($options[$userParam])) {
            $params["refine.$apiField"] = $options[$userParam];
        }
    }
    return $params;
}


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
    return is_array($data) ? $data : ['error' => 'Réponse invalide de l’API.'];
}

function getEtablissementsSupPublics(array $options = []): array {
    $params = buildEtablissementsApiParams($options);
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query($params);
    $data = callOpenDataApi($url);
    if (isset($data['error'])) {
        return ['error' => $data['error']];
    }
    return $data['records'] ?? ['error' => 'Aucune donnée reçue depuis l’API.'];
}

function getRegionsDepuisAPI(): array {
    $url = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search?" . http_build_query([
        'dataset' => 'fr-esr-implantations_etablissements_d_enseignement_superieur_publics',
        'rows' => 0,
        'facet' => 'reg_nom'
    ]);
    $response = file_get_contents($url);
    if (!$response) return [];
    $data = json_decode($response, true);
    if (!isset($data['facet_groups'][0]['facets'])) return [];
    return array_column($data['facet_groups'][0]['facets'], 'name');
}

function loadDepartements(string $regionName, string $csvPath): array {
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

function formatEtablissement(array $fields): array
{
    // Nom priorisé
    $nom = $fields['siege_lib']
        ?? $fields['ur_lib']
        ?? $fields['implantation_lib']
        ?? 'Nom inconnu';

    // Type
    $type = $fields['type_d_etablissement']
        ?? $fields['bcnag_n_nature_uai_libelle_editi']
        ?? 'Type inconnu';

    // Adresse complète
    $adresseParts = [];

    if (!empty($fields['adresse_uai'])) {
        $adresseParts[] = $fields['adresse_uai'];
    } elseif (!empty($fields['lieu_dit_uai'])) {
        $adresseParts[] = $fields['lieu_dit_uai'];
    }

    if (!empty($fields['code_postal_uai'])) {
        $adresseParts[] = $fields['code_postal_uai'];
    }

    if (!empty($fields['com_nom'])) {
        $adresseParts[] = $fields['com_nom'];
    }

    $adresse_complete = implode(', ', $adresseParts);

    // Services
    $services = [];
    if (!empty($fields['services'])) {
        $services = is_array($fields['services']) 
            ? $fields['services']
            : [$fields['services']];
    }

    // Coordonnées GPS
    $coordonnees = $fields['coordonnees'] ?? null;

    // Date d'ouverture
    $ouverture = $fields['date_ouverture'] ?? null;

    // Retour structuré
    return [
        'nom' => $nom,
        'type' => $type,
        'adresse' => $adresse_complete ?: 'Adresse inconnue',
        'services' => $services,
        'ouverture' => $ouverture,
        'coordonnees' => $coordonnees,
        'ville' => $fields['com_nom'] ?? '',
        'region' => $fields['reg_nom'] ?? '',
        'departement' => $fields['dep_nom'] ?? '',
        'academie' => $fields['aca_nom'] ?? '',
    ];
}


function renderEtablissementCard(array $etab): string
{
    $html = '<div class="etab-card">';
    $html .= '<h4>' . htmlspecialchars($etab['nom']) . '</h4>';
    $html .= '<p><strong>🏷️ Type :</strong> ' . htmlspecialchars($etab['type']) . '</p>';
    $html .= '<p><strong>📍 Adresse :</strong> ' . htmlspecialchars($etab['adresse']) . '</p>';

    if (!empty($etab['services'])) {
        $html .= '<p><strong>📚 Services :</strong> ' . htmlspecialchars(implode(', ', $etab['services'])) . '</p>';
    }

    if (!empty($etab['ouverture'])) {
        $html .= '<p><strong>🗓️ Ouverture :</strong> ' . htmlspecialchars($etab['ouverture']) . '</p>';
    }

    $html .= '</div>';
    return $html;
}







?>
