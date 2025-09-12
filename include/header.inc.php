<?php
// Vérifier si le cookie existe
if(isset($_COOKIE['visited'])) {
    $headerClass = "visited"; // nouvelle classe après refresh
} else {
    $headerClass = ""; // classe vide pour la première visite
    setcookie("visited", "true", time() + 7*24*60*60, "/"); // cookie 7 jours
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Mathis Albrun" />
    <meta name="date" content="2025-03-06T22:44:25+0100" />
    <meta name="description" content="<?php echo $description ?>" />
    <link rel="shortcut icon" type="image/png" href="/images/favicon.png"/>
    <title><?php echo $title ?></title>

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <a href="#">
        <img class="fleche" src="images/fleche.png" alt="image de fleche"/>
    </a>

    <header class="<?php echo $headerClass; ?>">
        <!-- Logo -->
        <a href="/index.php" class="logo">
            <img src="/images/favicon.png" alt="Logo Etudaviz" width="100"/>
        </a>

        <!-- Navigation -->
        <nav>
            <ul>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="orientation.php">Apprendre à s'orienter</a></li>
                <li><a href="apropos.php">À propos</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

       <!-- Zone droite : recherche + connexion -->
        <div class="header-right">
            <form class="search-bar" action="recherche.php" method="get">
                <input type="text" name="q" placeholder="Rechercher une formation..." />
                <button type="submit"><span>🔍</span></button>
            </form>
            <a href="login.php" class="btn-connexion">Connexion</a>
        </div>
    </header>

    <main>
        <h1><?php echo $h1 ?></h1>
