    <?php
    session_start();

	if (!isset($_SESSION['user'])) {
		header("Location: index.php");
		exit();
	}
    $title = "Page privée";
    $description = "Espace personnel utilisateur";
    $h1 = "Mon tableau de bord";

    $currentAvatar = $_SESSION['avatar'] ?? 'default-avatar.png';
    


    require "./include/header.inc.php";
    ?>

    <div class="dashboard-header">
        <div class="user-info">
            <img src="images/avatars/<?= htmlspecialchars($currentAvatar) ?>" class="avatar" alt="avatar par défault de l utilisateur">
            <div>
                <h2>Bonjour <?= htmlspecialchars(getPseudo()) ?> 👋</h2>
                <p>Dernière connexion : <span id="date-connexion">nouvelle visite</span></p>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					const spanDate = document.getElementById("date-connexion");
					const derniereVisite = localStorage.getItem("monSite_lastVisit");
					if (derniereVisite) {
						spanDate.textContent = derniereVisite;
					} else {
						spanDate.textContent = "Première visite";
					}
					const dateActuelle = new Date().toLocaleString("fr-FR", {
						day: 'numeric',
						month: 'long',
						hour: '2-digit',
						minute: '2-digit'
					});
					
					localStorage.setItem("monSite_lastVisit", dateActuelle);
				});
			</script>
            </div>
        </div>

        <div class="dashboard-actions" style = "display:grid">
            <a href="profil.php" class="btn-primary">Modifier mon profil</a>
            <a href="logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </div>



    <section class="dashboard-grid">

    <!-- Bloc 1 : Large, comme dans ton exemple -->
    <div class="card bloc1">
        <h3>Statistiques</h3>
        <ul>
            <li><strong>Connexions : a poursuivre avec la BD</strong></li>
            <li><strong>Dernière action :</strong> a poursuivre avec la BD</li>
        </ul>

    </div>

    <!-- Bloc 2 : Gauche -->
    <div class="card bloc2">
        <h3>Activité récente</h3>
        <div class="timeline">
            <p>📌 Consultation d’une formation (il y a 2h)</p>
            <p>📄 Mise à jour du profil (hier)</p>
        </div>
    </div>

    <!-- Bloc 3 : Droite -->
    <div class="card bloc3">
        <h3>Recommandations pour vous</h3>
        <ul>
            <li>👉 Formation : "Trouver son orientation"</li>
            <li>👉 Article : "Bien rédiger son CV"</li>
        </ul>
    </div>

</section>
    <?php
    require "./include/footer.inc.php";
    ?>
