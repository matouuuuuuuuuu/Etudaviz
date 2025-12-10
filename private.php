<?php
require "./include/functions.inc.php"; 
ensureSession();

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = currentUser();
$mesAvis = getAvisByUser($user['id']);


if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user']['id'] ?? null; 
$dbError = null;
$userReviews = [];

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

    <div class="card bloc1">
        <h3>Statistiques</h3>
        <ul>
            <li><strong>Connexions : a poursuivre avec la BD</strong></li>
            <li><strong>Dernière action :</strong> a poursuivre avec la BD</li>
        </ul>
    </div>

    <div class="card bloc2">
        <h3>Activité récente</h3>
        <div class="timeline">
            <p>📌 Consultation d’une formation (il y a 2h)</p>
            <p>📄 Mise à jour du profil (hier)</p>
        </div>
    </div>

    <div class="card bloc3">
        <h3>Recommandations pour vous</h3>
        <ul>
            <li>👉 Formation : "Trouver son orientation"</li>
            <li>👉 Article : "Bien rédiger son CV"</li>
        </ul>
    </div>

</section>

<section class="user-reviews-wrapper">
    <h2 style=" margin-bottom: 30px;">Mes avis publiés</h2>

    <?php if (empty($mesAvis)): ?>
        <p>Vous n’avez encore publié aucun avis.</p>

    <?php else: ?>
        <div class="user-reviews-section">
            <?php foreach ($mesAvis as $avis): ?>
                <div class="avis-card">
                    <h3><?= htmlspecialchars($avis['titre_avis']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($avis['description'])) ?></p>
                    <small>
                        Expérience du : <?= htmlspecialchars($avis['date_experience']) ?><br>
                        Publié le : <?= htmlspecialchars($avis['date_publication']) ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<section class="user-benefits">
  <div class="container">
    <h2>Vos avantages en tant que membre de la communauté Etudaviz</h2>
    <p>En étant connecté à votre espace personnel, vous pouvez :</p>
    <ul>
      <li>Calculer la distance entre votre domicile et nos établissements.</li>
      <li>Consulter les avis des autres utilisateurs.</li>
      <li>Contribuer et partager vos expériences sur notre blog.</li>
    </ul>
  </div>
</section>

<?php
require "./include/footer.inc.php";
?>