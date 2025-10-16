<?php
if(isset($_COOKIE['visited'])) {
    $headerClass = "visited"; 
} else {
    $headerClass = ""; 
    setcookie("visited", "true", time() + 7*24*60*60, "/"); 
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
        <a href="/index.php" class="logo">
            <img src="/images/favicon.png" alt="Logo personnel"/>
        </a>

        <nav>
          <ul class="menu">
            <li>
              <a href="formations.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">Formations</a>
              <ul class="submenu">
                <li><a href="#">A définir</a></li>
                <li><a href="#">A définir</a></li>
              </ul>
            </li>
            
            <li>
              <a href="orientation.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">Apprendre à s'orienter</a>
              <ul class="submenu">
                <li><a href="#">A définir</a></li>
                <li><a href="#">A définir</a></li>
                <li><a href="#">A définir</a></li>
              </ul>
            </li>

            <li>
              <a href="apropos.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">À propos</a>
              <ul class="submenu">
                <li><a href="#">A définir</a></li>
                <li><a href="#">A définir</a></li>
                <li><a href="#">A définir</a></li>
              </ul>
            </li>

            <li>
              <a href="contact.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">Contact</a>
            </li>
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
