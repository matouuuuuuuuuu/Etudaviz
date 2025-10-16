<?php
require './include/functions.inc.php';

$limit = 10;
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$etablissements = getEtablissementsSupPublics([
    'limit' => $limit,
    'offset' => $offset,
    'region' => $_GET['region'] ?? null,
    'departement' => $_GET['departement'] ?? null,
    'type' => $_GET['type'] ?? null,
    'search' => $_GET['search'] ?? null,
]);

$resultats = [];
if (!isset($etablissements['error']) && !empty($etablissements)) {
    foreach ($etablissements as $record) {
        $fields = $record['fields'];
        $resultats[] = [
            'nom' => $fields['siege_lib'] ?? $fields['ur_lib'] ?? $fields['implantation_lib'] ?? 'Nom inconnu',
            'type' => $fields['type_d_etablissement'] ?? $fields['nature_uai'] ?? 'Type inconnu',
            'adresse' => $fields['adresse_uai'] ?? $fields['lieu_dit_uai'] ?? $fields['com_nom'] ?? 'Adresse inconnue'
        ];
    }
}

foreach ($resultats as $etab): ?>
    <li>
        <strong><?= htmlspecialchars($etab['nom']) ?></strong><br>
        Type : <?= htmlspecialchars($etab['type']) ?><br>
        Adresse : <?= htmlspecialchars($etab['adresse']) ?><br><br>
    </li>
<?php endforeach; ?>
