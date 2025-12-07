<?php
require './include/functions.inc.php';

$limit = 6;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min(100, $page)); // jamais + de 100 pages

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
        $formatted = formatEtablissement($record['fields'], $record['recordid']);
        if (!empty($formatted)) {
            echo renderEtablissementCard($formatted);
        }
    }
}
 
