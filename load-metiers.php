<?php
require "./include/functions.inc.php";

$query = $_GET["q"] ?? "";
$page  = (int)($_GET["page"] ?? 1);

$limit  = 4;
$offset = ($page - 1) * $limit;

if (strlen($query) < 2) {
    exit; // rien à renvoyer
}

$results = escoSearch($query, $limit, $offset);

if (empty($results)) {
    exit; // renvoie juste une chaîne vide
}

foreach ($results as $m) {
    echo renderMetierCard($m);
}
