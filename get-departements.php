<?php
require './include/functions.inc.php';

$region = $_GET['region'] ?? '';
$csvPath = './data/csv/departements_region.csv'; // adapte le chemin

header('Content-Type: application/json');

if (!$region) {
    echo json_encode([]);
    exit;
}

$departements = loadDepartements($region, $csvPath);
echo json_encode($departements);
