<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$title = "Contact - Etudaviz";
$description = "Formulaire de contact Etudaviz";
$h1 = "Contactez-nous";
$canonical = "https://etudaviz.alwaysdata.net/contact.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";
require "../config/config-mail.inc.php";

ensureSession();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    captchaInit();
}

$erreur  = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom     = trim($_POST['nom'] ?? '');
    $mail    = trim($_POST['mail'] ?? '');
    $sujet   = trim($_POST['sujet'] ?? '');
    $contenu = trim($_POST['message'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');

    if ($nom === '' || $mail === '' || $sujet === '' || $contenu === '' || $captcha === '') {
        $erreur = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse mail invalide.";
    } elseif (!captchaCheck($captcha)) {
        $erreur = "Captcha incorrect.";
    } else {
        if (sendContactMail($mail, $nom, $sujet, $contenu)) {
            $message = "Merci, votre message a bien été envoyé.";
            captchaInit(); 
        } else {
            $erreur = "Impossible d'envoyer votre message pour le moment. Merci de réessayer plus tard.";
        }
    }
}

require "./include/header.inc.php";
?>

<div class="login-container">
    <div class="login-box">
        <h2>Contact</h2>

        <?php if ($erreur): ?>
            <div class="error" style="color:red; text-align:center; margin-bottom:10px;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message" style="color:green; text-align:center; margin-bottom:10px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php $captchaQuestion = captchaQuestion(); ?>

        <form method="POST">
            <input type="text" name="nom" placeholder="Votre nom" required>
            <input type="email" name="mail" placeholder="Votre adresse mail" required>
            <input type="text" name="sujet" placeholder="Sujet" required>
            <textarea name="message" placeholder="Votre message..." rows="5" required></textarea>
            <label for="captcha">
            Captcha : combien font <?= htmlspecialchars($captchaQuestion) ?> ?
            </label>
            <input type="text" name="captcha" id="captcha" required>
            <button type="submit">Envoyer</button>
        </form>

        <div class="login-links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
