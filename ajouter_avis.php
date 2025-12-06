<?php
require "./include/functions.inc.php";
ensureSession();

if (!isLoggedIn()) {
    die("Vous devez être connecté pour déposer un avis.");
}

$id_formation     = $_POST['id_formation']     ?? null;
$titre            = $_POST['titre_avis']       ?? null;
$description      = $_POST['description']      ?? null;
$date_experience  = $_POST['date_experience']  ?? null;

$id_utilisateur = currentUser()['id']; // ✔ Récupération propre

if (!$id_formation || !$titre || !$description || !$date_experience) {
    die("Formulaire incomplet.");
}

$stmt = $pdo->prepare("
    INSERT INTO Avis 
    (id_formation, id_utilisateur, titre_avis, description, date_experience, statut)
    VALUES (?, ?, ?, ?, ?, 'actif')
");

$stmt->execute([
    $id_formation,
    $id_utilisateur,
    $titre,
    $description,
    $date_experience
]);

header("Location: fiche_formation.php?id=" . $id_formation . "&avis=ok");
exit;
