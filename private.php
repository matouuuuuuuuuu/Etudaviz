<?php
require "./include/functions.inc.php"; 
ensureSession();

// Vérification de connexion
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Récupération de l'utilisateur courant
$user = currentUser();
$userId = $user['id'] ?? null;

// Sécurité : si la session n'existe pas, redirection
if (!$userId) {
    header("Location: index.php");
    exit();
}

// Met à jour les statistiques de connexion
updateUserLoginStats($pdo, $userId);

// Récupération des avis de l'utilisateur
$mesAvis = getAvisByUser($userId);
$recentActivity = getRecentActivities($pdo, $userId, 5);

// Initialisation des variables
$dbError = null;
$userReviews = [];
$currentAvatar = 'default-avatar.png';

// Récupération de l'avatar depuis la base
$stmt = $pdo->prepare("SELECT avatar, last_login, login_count FROM Utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

if ($stats) {
    if (!empty($stats['avatar'])) {
        $currentAvatar = $stats['avatar'];
    }
    $nbConnexions = $stats['login_count'] ?? 0;
    $derniereConnexion = $stats['last_login'] ?? 'Aucune connexion';
} else {
    $nbConnexions = 0;
    $derniereConnexion = 'Aucune connexion';
}

// Titre et description de la page
$title = "Page privée";
$description = "Espace personnel utilisateur";
$h1 = "Mon tableau de bord";

require "./include/header.inc.php";
?>



<div class="dashboard-header">
    <div class="user-info">
        <img src="images/avatars/<?= htmlspecialchars($currentAvatar) ?>" class="avatar" alt="Avatar de l’utilisateur">
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

    <div class="dashboard-actions" style="display: grid; gap: 10px;">
        <button onclick="window.location.href='profil.php'" class="btn-primary-private">Modifier mon profil</button>
        <button onclick="window.location.href='logout.php'" class="btn-primary-private">Déconnexion</button>
    </div>
</div>


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

<section class="dashboard-grid">
    <div class="card bloc2">
        <h3>Statistiques</h3>
        <ul>
            <li><strong>Connexions :</strong> <?= htmlspecialchars($nbConnexions) ?></li>
            <li><strong>Dernière connexion :</strong> <?= htmlspecialchars($derniereConnexion) ?></li>
        </ul>
    </div>


    <div class="card bloc3">
        <h3>Activité récente</h3>
        <div class="puceted-list">
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $act): ?>
                    <p>
                        <?= $act['type'] === 'avis' ? '📌 Avis publié : ' : '🖼️ Image uploadée : ' ?>
                        <?= htmlspecialchars($act['description']) ?> 
                        (<?= htmlspecialchars($act['date_action']) ?>)
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune activité récente.</p>
            <?php endif; ?>
        </div>
    </div>

</section>

<section class="user-reviews-wrapper">
    <h2 style=" margin-bottom: 30px;">Mes avis publiés</h2>

    <?php if (empty($mesAvis)): ?>
        <p>Vous n’avez encore publié aucun avis.</p>

    <?php else: ?>
        <div class="user-reviews-section">
            <?php foreach ($mesAvis as $avis): ?>
                <div class="user-review-card">
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

<?php
require "./include/footer.inc.php";
?>