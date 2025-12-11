<?php
require "./include/functions.inc.php";

ensureSession();
header('Content-Type: application/json');

// 1) Vérifier que l'utilisateur est connecté
$user = currentUser();
if (!$user || empty($user['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'not_logged_in']);
    exit;
}

$type      = $_POST['type'] ?? '';
$idElement = $_POST['id'] ?? '';

if (!in_array($type, ['formation', 'metier'], true) || $idElement === '') {
    echo json_encode(['status' => 'error', 'message' => 'bad_params']);
    exit;
}

// 2) Connexion BDD via ta fonction
$pdo = getDBConnection();
var_dump($pdo);
exit;
if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'db_error']);
    exit;
}

$idUtilisateur = (int) $user['id'];

// 3) Vérifier si le favori existe déjà
$sql = "
    SELECT id_favori
    FROM Favoris
    WHERE id_utilisateur = :id_utilisateur
      AND type = :type
      AND id_element = :id_element
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_utilisateur' => $idUtilisateur,
    ':type'           => $type,
    ':id_element'     => $idElement,
]);

if ($stmt->fetch()) {
    // 4a) Il existe → on le supprime
    $del = $pdo->prepare("
        DELETE FROM Favoris
        WHERE id_utilisateur = :id_utilisateur
          AND type = :type
          AND id_element = :id_element
    ");
    $del->execute([
        ':id_utilisateur' => $idUtilisateur,
        ':type'           => $type,
        ':id_element'     => $idElement,
    ]);

    echo json_encode(['status' => 'removed']);
} else {
    // 4b) Il n'existe pas → on l'ajoute
    $add = $pdo->prepare("
        INSERT INTO Favoris (id_utilisateur, type, id_element)
        VALUES (:id_utilisateur, :type, :id_element)
    ");
    $add->execute([
        ':id_utilisateur' => $idUtilisateur,
        ':type'           => $type,
        ':id_element'     => $idElement,
    ]);

    echo json_encode(['status' => 'added']);
}
