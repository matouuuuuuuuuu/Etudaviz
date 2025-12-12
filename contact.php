<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

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
            <label for="nom" class="visually-hidden">Votre nom</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" required>

            <label for="mail" class="visually-hidden">Votre adresse mail</label>
            <input type="email" id="mail" name="mail" placeholder="Votre adresse mail" required>

            <label for="sujet" class="visually-hidden">Sujet</label>
            <input type="text" id="sujet" name="sujet" placeholder="Sujet" required>

            <label for="message" class="visually-hidden">Votre message</label>
            <textarea id="message" name="message" placeholder="Votre message..." rows="5" required></textarea>

            <label for="captcha">
                Captcha : combien font <?= htmlspecialchars($captchaQuestion) ?> ?
            </label>
            <input type="text" id="captcha" name="captcha" required>

            <button type="submit">Envoyer</button>
        </form>


        <div class="login-links">
            <a  style="color:black;" href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
