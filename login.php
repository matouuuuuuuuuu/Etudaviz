<?php
$title = "Connexion à votre compte Etudaviz";
$description = "Page permettant de se connecter à votre compte Etudaviz";
$h1 = "Connexion à votre espace Etudaviz";



require "./include/functions.inc.php"; 
require "../config/bdconnect.php";    

ensureSession();

require "./include/header.inc.php";

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifiant = trim($_POST['identifiant'] ?? '');
    $password    = $_POST['password'] ?? '';

    if ($identifiant !== '' && $password !== '') {

        $user = verifyLoginDb($identifiant, $password);

        if ($user !== null) {
            
            loginUser($user);

            header('Location: private.php');
            exit;
        } else {
            $erreur = "Identifiant (pseudo ou mail) ou mot de passe incorrect.";
        }

    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<div class="login-container">
    <div class="login-box">
        <h2>Connexion</h2>

        <?php if ($erreur): ?>
            <div class="error" style="color:red; text-align:center;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="identifiant"
                   placeholder="Pseudo ou adresse mail" required>

            <input type="password" name="password"
                   placeholder="Mot de passe" required>

            <button type="submit">Se connecter</button>
        </form>

        <div class="login-links">
            <a href="mdp-oublie.php">Mot de passe oublié ?</a><br>
            <a href="inscription.php">Je n'ai pas encore de compte. Créer un compte</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
