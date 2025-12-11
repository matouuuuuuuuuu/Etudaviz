<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	if(isset($_COOKIE['visited'])) {
		$headerClass = "visited"; 
	} else {
		$headerClass = ""; 
		setcookie("visited", "true", time() + 7*24*60*60, "/"); 
	}
	require_once __DIR__ . "/functions.inc.php";
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
    <link id="theme-link" rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Text:wght@200..700&display=swap" rel="stylesheet">
	<?php
      if (empty($canonical)) {
          $canonical = 'https://etudaviz.alwaysdata.net' . strtok($_SERVER['REQUEST_URI'], '?');
      }
    ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES); ?>">
</head>

<body>




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
						<li><a href="test-orientation.php">Test d'orientation</a></li>
						<li><a href="metiers.php">Découvrir des métiers</a></li>
						<li><a href="avis.php">Avis</a></li>
					</ul>
				</li>
				<li>
					<a href="apropos.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">À propos</a>
				
				</li>
				<li>
					<a href="contact.php"><img src="images/fleche-droite.png" alt="Flèche" class="nav-icon">Contact</a>
				</li>
			</ul>
		</nav>

		<div class="header-right">
			<form class="header-search-bar" action="recherche.php" method="get">
				<label for="search-input" class="visually-hidden">Rechercher une formation</label>
				<input 
					type="text" 
					id="search-input" 
					name="q" 
					placeholder="Rechercher une formation..."
				/>
				<button type="submit" class="search-button" aria-label="Rechercher" style="opacity: 1; color: rgb(0, 100, 80); background-color: rgb(255, 255, 255);">
					🔍
				</button>
			</form>

			<button id="theme-toggle" class="theme-toggle" aria-label="Changer le thème">
				<img id="theme-icon" src="/images/lune.png" alt="Mode clair">
			</button>

			<?php if (isLoggedIn()): ?>
				<a href="private.php">
					<img src="/images/avatars/<?= htmlspecialchars($_SESSION['avatar'] ?? 'default-avatar.png') ?>" class="header-avatar">
				</a>
			<?php else: ?>

				<a href="login.php" class="btn-connexion">Connexion</a>

			<?php endif; ?>
		</div>


	</header>


    <main>
        <h1><?php echo $h1 ?></h1>
