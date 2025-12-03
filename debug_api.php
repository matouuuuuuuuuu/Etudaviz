<?php
require "./include/functions.inc.php";

$options = [
    'limit' => 50,
    'search' => 'droit',
];

$data = getEtablissementsSupPublics($options);

// Normalisation + dédoublonnage
$normalized = normalizeAndGroupFormations($data);

// Affichage
echo "<h2>AVANT (API brute)</h2>";
echo count($data) . " résultats<br><br>";

echo "<h2>APRÈS dédoublonnage</h2>";
echo count($normalized) . " résultats<br><br>";

echo "<pre>";
var_dump($normalized);
echo "</pre>";
