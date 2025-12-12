<?php
$title = "Connexion à votre compte Etudaviz";
$description = "Page permettant de se connecter à votre compte Etudaviz";
$h1 = "Connexion à votre espace Etudaviz";

require "./include/functions.inc.php"; 
require "../config/bdconnect.php";    
ensureSession();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifiant = trim($_POST['identifiant'] ?? '');
    $password    = $_POST['password'] ?? '';

    if ($identifiant !== '' && $password !== '') {

        $user = verifyLoginDb($identifiant, $password);

        if ($user !== null) {

            $erreurStatut = checkStatutCompte($user['statut_compte'] ?? null);

            if ($erreurStatut !== null) {
                $erreur = $erreurStatut;
            } else {
                loginUser($user);

                header('Location: index.php');
                exit;
            }

        } else {
            $erreur = "Identifiant (pseudo ou mail) ou mot de passe incorrect.";
        }

    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

require "./include/header.inc.php";
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
            <label for="identifiant" class="visually-hidden">Pseudo ou adresse mail</label>
            <input type="text" id="identifiant" name="identifiant" placeholder="Pseudo ou adresse mail" required>

            <label for="password" class="visually-hidden">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>

            <button type="submit">Se connecter</button>
        </form>


        <div class="login-links">
            <a  style="color:black;" href="mdp-oublie.php">Mot de passe oublié ?</a>
            <a style="color:black;" href="inscription.php">Je n'ai pas encore de compte. Créer un compte</a>
        </div>
    </div>
</div>

<?php require "./include/footer.inc.php"; ?>
