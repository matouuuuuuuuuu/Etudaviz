<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

$title = "Mot de passe oublié - Etudaviz";
$description = "Page vous permettant de réinitialiser votre mot de passe";
$h1 = "Réinitialiser votre mot de passe";
$canonical = "https://etudaviz.alwaysdata.net/mdp-oublie.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";
require "../config/config-mail.inc.php";
require "../config/recaptcha.inc.php";
require "./include/header.inc.php";

$erreur = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail'] ?? '');

    $recaptchaToken = trim($_POST['g-recaptcha-response'] ?? '');

    if ($mail === '' || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse mail invalide.";
    } elseif (!verifRecaptchaV3($recaptchaToken, 'reset_password')) {
        $erreur = "Vérification anti-robot échouée.";
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
                        $message = "Si un compte existe avec cette adresse, un email de réinitialisation a été envoyé.";
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

        <form method="POST" id="forgot-form">
            <label for="mail" class="visually-hidden">Adresse mail</label>
            <input type="email" id="mail" name="mail" placeholder="Votre adresse mail" required>

            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

            <button type="submit">Envoyer le lien</button>
        </form>

        <div class="login-links">
            <a style="color:black;" href="login.php">Retour à la connexion</a>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=<?= RECAPTCHA_SITE_KEY ?>"></script>
<script>
  const form = document.getElementById('forgot-form');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    grecaptcha.ready(function () {
      grecaptcha.execute("<?= RECAPTCHA_SITE_KEY ?>", {action: "reset_password"}).then(function (token) {
        document.getElementById('g-recaptcha-response').value = token;
        form.submit();
      });
    });
  });
</script>

<?php require "./include/footer.inc.php"; ?>
