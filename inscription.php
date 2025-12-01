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
                $baseUrl = "https://" . $_SERVER['HTTP_HOST'];
                $lienConnexion = $baseUrl . "/login.php";

                if (sendVerificationMail($mail, $pseudo, $lienConnexion)) {
                    $message = "Compte créé ! Un email de confirmation vous a été envoyé.";
                } else {
                    $message = "Compte créé, mais impossible d'envoyer l'email de confirmation.";
                }
                //$message = "Compte créé ! (test sans envoi d'email)";

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
            <input type="text" name="pseudo" placeholder="Pseudo" required>

            <input type="email" name="mail" placeholder="Adresse mail" required>

            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="password" name="password2" placeholder="Confirmation du mot de passe" required>

            <label for="captcha">
                Captcha : combien font <?= htmlspecialchars($captchaQuestion) ?> ?
            </label>
            <input type="text" name="captcha" id="captcha" required>

            <button type="submit">Créer mon compte</button>
        </form>

        <div class="login-links">
            <a href="login.php">J'ai déjà un compte. Me connecter</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
