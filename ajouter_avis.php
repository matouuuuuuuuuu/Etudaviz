<?php
require "./include/functions.inc.php";
ensureSession();

if (!isLoggedIn()) {
    header("Location: login.php?error=connexion");
    exit;
}

// Récupération des données du formulaire
$id_formation     = $_POST['id_formation']     ?? null;
$titre            = $_POST['titre_avis']       ?? null;
$description      = $_POST['description']      ?? null;
$date_experience  = $_POST['date_experience']  ?? null;
$notes            = $_POST['note']             ?? []; // tableau des notes des critères

$id_utilisateur = currentUser()['id'];

// Validation des champs obligatoires
if (!$id_formation || !$titre || !$description || !$date_experience) {
    header("Location: fiche_formation.php?id=$id_formation&avis=erreur");
    exit;
}

try {
    // Commencer une transaction pour garantir la cohérence
    $pdo->beginTransaction();

    // Insertion de l'avis dans Avis
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

    // Récupération de l'id de l'avis inséré
    $idAvis = $pdo->lastInsertId();

    // Insertion des notes dans Note_Critere si présentes
    if (!empty($notes) && is_array($notes)) {
        $stmtNote = $pdo->prepare("
            INSERT INTO Note_Critere (id_avis, nom_critere, valeur)
            VALUES (?, ?, ?)
        ");

        foreach ($notes as $critere => $valeur) {
            // Cast en int pour sécurité
            $stmtNote->execute([
                $idAvis,
                $critere,
                (int)$valeur
            ]);
        }
    }

    // Valider la transaction
    $pdo->commit();

    // Redirection vers la fiche formation avec succès
    header("Location: fiche_formation.php?id=" . $id_formation . "&avis=ok");
    exit;

} catch (Exception $e) {
    // En cas d'erreur, rollback et redirection
    $pdo->rollBack();
    header("Location: fiche_formation.php?id=" . $id_formation . "&avis=erreur");
    exit;
}
