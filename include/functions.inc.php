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
?>
