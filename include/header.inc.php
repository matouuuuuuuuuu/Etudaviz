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
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" type="image/png" href="/images/favicon.png"/>
    <title><?php echo $title ?></title>
    <link id="theme-link" rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Text:wght@200..700&display=swap" rel="stylesheet">
<body>
    <a href="#">
        <img class="fleche" src="images/fleche.png" alt="image de fleche"/>
    </a>
	<script>
		// Affiche la flèche quand l'utilisateur scrolle
		const fleche = document.querySelector('.fleche');
		fleche.style.display = 'none';
		window.addEventListener('scroll', function() {
			if (window.scrollY > 100) { // tu peux ajuster la valeur
				fleche.style.display = 'block';
			} else {
				fleche.style.display = 'none';
			}
		});
	</script>

    <header class="<?php echo $headerClass; ?>">
		<div class="header-left">
			<a href="/index.php" class="logo">
				<img src="/images/favicon.png" alt="Logo personnel"/> 
			</a>
			<button class="burger">☰</button> <!-- uniquement visible mobile -->
			<script>
				document.addEventListener('DOMContentLoaded', () => {
					const burger = document.querySelector('.burger');
					const menu = document.querySelector('nav ul.menu'); // bien cibler le <ul>

					burger.addEventListener('click', () => {
						menu.classList.toggle('open');
					});

					document.querySelectorAll('.menu li').forEach(li => {
						const submenu = li.querySelector('.submenu');
						if(submenu){
							li.addEventListener('click', e => {
								if(window.innerWidth <= 768){
									e.stopPropagation();
									submenu.classList.toggle('open');
								}
							});
						}
					});
				});
			</script>
		</div>

		<nav>
			<ul class="menu">
				<li>
					<a href="formations.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">Formations</a>
					<ul class="submenu">
						<li><a href="formations.php?region=&departement=&type=Formations+en+universit%C3%A9">Université</a></li>
						<li><a href="formations.php?region=&departement=&type=BTS+-+BTSA+-+BTSM">BTS</a></li>
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

		<div class="header-right">
			<form class="header-search-bar" action="recherche.php" method="get">
				<input type="text" name="q" placeholder="Rechercher une formation..." />
				<button type="submit">🔍</button>
			</form>
			<button id="theme-toggle" class="theme-toggle" aria-label="Changer le thème">
				<img id="theme-icon" src="/images/lune.png" alt="Mode clair">
			</button>
			<a href="login.php" class="btn-connexion">Connexion</a>
		</div>
	</header>


    <main>
        <h1><?php echo $h1 ?></h1>
