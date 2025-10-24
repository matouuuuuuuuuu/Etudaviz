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

if (!isset($etablissements['error']) && !empty($etablissements)) {
    foreach ($etablissements as $record) {
        echo renderEtablissementCard(formatEtablissement($record['fields']));
    }
}
 
