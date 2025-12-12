<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

$redirect = false;
$title = "Réinitialisation du mot de passe - Etudaviz";
$description = "Définir un nouveau mot de passe";
$h1 = "Réinitialiser votre mot de passe";
$canonical = "https://etudaviz.alwaysdata.net/reset-mdp.php";

require "./include/functions.inc.php";
require "../config/bdconnect.php";

ensureSession(); 
require "./include/header.inc.php";

$erreur = "";
$message = "";

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    $erreur = "Lien invalide.";
} else {
    $user = findUserByToken($pdo, $token);

    if ($user === null) {
        $erreur = "Lien invalide ou expiré.";
    } else {

        $erreurStatut = checkStatutCompte($user['statut_compte'] ?? null, 'reset_password');
        if ($erreurStatut !== null) {
            $erreur = $erreurStatut;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password  = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            if ($password === '' || $password2 === '') {
                $erreur = "Veuillez remplir tous les champs.";
            } elseif ($password !== $password2) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                $ok = resetPasswordWithToken($pdo, (int)$user['id_utilisateur'], $token, $password);

            if ($ok) {
                $message = "Votre mot de passe a été modifié. Vous allez être redirigé vers la page d’accueil dans 15 secondes.";
                $redirect = true;  
            } else {
                    $erreur = "Impossible de réinitialiser le mot de passe. Le lien est peut-être expiré.";
                }
            }
        }
    }
}
?>

<div class="login-container">
    <div class="login-box">
        <h2>Nouveau mot de passe</h2>

        <?php if ($erreur): ?>
            <div class="error" style="color:red; text-align:center;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message" style="color:green; text-align:center;">
                <?= htmlspecialchars($message) ?>
            </div>
        
        <?php if (!empty($redirect)): ?>
            <div style="text-align:center; margin-top:10px;">
                <a style="color:black;" href="index.php">Cliquez ici si vous n'êtes pas redirigé</a>
            </div>
        <script>
            setTimeout(() => {
            window.location.href = "index.php";
            }, 15000);
        </script>
        <?php endif; ?>

            <div class="login-links">
                <a style="color:black;" href="login.php">Retour à la connexion</a>
            </div>
        <?php else: ?>
            <?php if (!$erreur): ?>
                <form method="POST">
                    <label for="password" class="visually-hidden">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Nouveau mot de passe" required>

                    <label for="password2" class="visually-hidden">Confirmation</label>
                    <input type="password" id="password2" name="password2" placeholder="Confirmation" required>

                    <button type="submit">Modifier</button>
                </form>

                <div class="login-links">
                    <a style="color:black;" href="login.php">Retour à la connexion</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
