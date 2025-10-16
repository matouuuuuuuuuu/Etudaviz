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

function buildEtablissementsApiParams(array $options = []): array
{
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

    // Recherche libre
    if (!empty($options['search'])) {
        $params['q'] = $options['search'];
    }

    // Pagination : décalage (start)
    if (!empty($options['offset'])) {
        $params['start'] = (int)$options['offset'];
    }

    // Correspondance des filtres utilisateur → champs API
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


function callOpenDataApi(string $url): array
{
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

function getEtablissementsSupPublics(array $options = []): array
{
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




?>
