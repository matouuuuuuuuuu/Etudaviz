<?php
$title = "Activation de votre compte Etudaviz";
$description = "Activation du compte Etudaviz via le lien reçu par email";
$h1 = "Activation de votre compte";
$canonical = "https://etudaviz.alwaysdata.net/activation.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";

ensureSession();

$message = '';

if (!empty($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT id_utilisateur, statut_compte
            FROM Utilisateur
            WHERE token_activation = :token
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = "Lien d'activation invalide ou déjà utilisé.";
    } else {
        if ($user['statut_compte'] === 'actif') {
            $message = "Votre compte est déjà activé. Vous pouvez vous connecter.";
        } else {
            $sql = "UPDATE Utilisateur
                    SET statut_compte = 'actif',
                        token_activation = NULL
                    WHERE id_utilisateur = :id";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute(['id' => $user['id_utilisateur']])) {
                $message = "Votre compte a bien été activé. Vous pouvez maintenant vous connecter.";
            } else {
                $message = "Une erreur est survenue lors de l'activation de votre compte.";
            }
        }
    }
} else {
    $message = "Aucun token d'activation fourni.";
}

require "./include/header.inc.php";
?>

<div class="login-container">
    <div class="login-box">
        <h2>Activation du compte</h2>

        <p style="text-align:center;">
            <?= htmlspecialchars($message) ?>
        </p>

        <div class="login-links">
            <a href="login.php">Aller à la page de connexion</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
