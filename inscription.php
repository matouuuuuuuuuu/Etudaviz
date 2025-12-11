<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$title = "Inscription au site Etudaviz";
$description = "Créer un compte Etudaviz";
$h1 = "Créer votre compte Etudaviz";
$canonical = "https://etudaviz.alwaysdata.net/inscription.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";         
require "../config/config-mail.inc.php";    
ensureSession();

$erreur = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    captchaInit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo     = trim($_POST['pseudo'] ?? '');
    $mail       = trim($_POST['mail'] ?? '');
    $password   = $_POST['password'] ?? '';
    $password2  = $_POST['password2'] ?? '';
    $captchaAns = trim($_POST['captcha'] ?? '');

    $erreur = validateRegistrationInput($pseudo, $mail, $password, $password2, $captchaAns);

    if ($erreur === null) {
        if (isPseudoOrMailUsed($pdo, $pseudo, $mail)) {
            $erreur = "Ce pseudo ou cet email est déjà utilisé.";
        } else {
            $idUtilisateur = createUser($pdo, $pseudo, $mail, $password);

            if ($idUtilisateur === null) {
                $erreur = "Erreur lors de la création du compte.";
            } else {
                // Générer un token d'activation et envoyer un mail avec le lien
                $token = createActivationToken($pdo, $idUtilisateur);

                if ($token === null) {
                    $erreur = "Compte créé, mais impossible de générer le lien d'activation. Contactez l'administrateur.";
                } else {
                    $baseUrl = "https://" . $_SERVER['HTTP_HOST'];
                    $lienActivation = $baseUrl . "/activation.php?token=" . urlencode($token);

                    if (sendVerificationMail($mail, $pseudo, $lienActivation)) {
                        $message = "Compte créé ! Un email d'activation vous a été envoyé.";
                    } else {
                        $message = "Compte créé, mais impossible d'envoyer l'email d'activation.";
                    }
                }
                captchaInit();
            }
        }
    }

}

$captchaQuestion = captchaQuestion();

require "./include/header.inc.php";
?>

<div class="login-container">
    <div class="login-box">
        <h2>Inscription</h2>

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
            <label for="pseudo" class="visually-hidden">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" required>

            <label for="mail" class="visually-hidden">Adresse mail</label>
            <input type="email" id="mail" name="mail" placeholder="Adresse mail" required>

            <label for="password" class="visually-hidden">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>

            <label for="password2" class="visually-hidden">Confirmation du mot de passe</label>
            <input type="password" id="password2" name="password2" placeholder="Confirmation du mot de passe" required>

            <label for="captcha">
                Captcha : combien font <?= htmlspecialchars($captchaQuestion) ?> ?
            </label>
            <input type="text" name="captcha" id="captcha" required>

            <button type="submit">Créer mon compte</button>
        </form>


        <div class="login-links">
            <a style="color:black;" href="login.php">J'ai déjà un compte. Me connecter</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
