<?php
	$title="Connexion";
	$description="Page permettant de se connecter à votre compte Etudaviz";
    $h1="Connexion à votre espace Etudaviz";
    require "./include/header.inc.php";
?>

<main>
    <div class="login-container">
        <div class="login-box">
            <h2>Connexion</h2>

            <!-- Message d'erreur (optionnel) -->
            <div class="error">Email ou mot de passe incorrect.</div>

            <form>
                <input type="email" name="email" placeholder="Adresse email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>

            <a href="#">Créer un compte</a>
            <a href="#">Mot de passe oublié ?</a>
        </div>
    </div>
</main>

<?php
    require "./include/footer.inc.php";
?>

