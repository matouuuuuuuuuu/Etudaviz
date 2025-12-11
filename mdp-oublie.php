<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$title = "Mot de passe oublié - Etudaviz";
$description = "Page vous permettant de réinitialiser votre mot de passe";
$h1 = "Réinitialiser votre mot de passe";
$canonical = "https://etudaviz.alwaysdata.net/mdp-oublie.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";
require "../config/config-mail.inc.php";

require "./include/header.inc.php";

$erreur = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail'] ?? '');

    if ($mail === '' || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse mail invalide.";
    } else {
        $user = findUserByEmail($pdo, $mail);

        if ($user === null) {
            $message = "Si un compte existe avec cette adresse, un email de réinitialisation a été envoyé.";
        } else {
            $erreurStatut = checkStatutCompte($user['statut_compte'] ?? null, 'reset_password');

            if ($erreurStatut !== null) {
                $erreur = $erreurStatut;
            } else {
                $token = createPasswordResetToken($pdo, (int)$user['id_utilisateur']);

                if ($token === null) {
                    $erreur = "Impossible de générer un lien de réinitialisation. Réessayez plus tard.";
                } else {
                    $baseUrl = "https://" . $_SERVER['HTTP_HOST'];
                    $lienReset = $baseUrl . "/reset-mdp.php?token=" . urlencode($token);

                    if (sendPasswordResetMail($user['mail'], $user['pseudo'], $lienReset)) {
                        $message = "Un email de réinitialisation vous a été envoyé.";
                    } else {
                        $erreur = "Impossible d'envoyer l'email de réinitialisation pour le moment.";
                    }
                }
            }
        }
    }
}
?>

<div class="login-container">
    <div class="login-box">
        <h2>Mot de passe oublié</h2>

        <?php if ($erreur): ?>
            <div class="error" style="color:red; text-align:center;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message" style="color:green; text-align:center;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="mail" class="visually-hidden">Adresse mail</label>
            <input type="email" id="mail" name="mail" placeholder="Votre adresse mail" required>
            <button type="submit">Envoyer le lien</button>
        </form>

        <div class="login-links">
            <a style="color:black;" href="login.php">Retour à la connexion</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
