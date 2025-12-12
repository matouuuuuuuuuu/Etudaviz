<?php
session_start();
require "./include/functions.inc.php"; 
ensureSession();

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = currentUser();
$userId = $user['id'] ?? null;

// Liste des avatars disponibles
$avatars = [
    "avatar1.png",
    "avatar2.png",
    "avatar3.png",
    "avatar5.png",
    "avatar6.png",
    "avatar7.png",
    "avatar8.png",
    "avatar9.png",
    "avatar10.png",
];

$currentAvatar = "default-avatar.png";

// Récupérer l'avatar actuel depuis la base
if ($userId) {
    $stmt = $pdo->prepare("SELECT avatar FROM Utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$userId]);
    $avatar = $stmt->fetchColumn();
    if ($avatar) {
        $currentAvatar = $avatar;
    }
}

// Enregistrement du nouvel avatar si formulaire soumis
if (isset($_POST['selected_avatar']) && in_array($_POST['selected_avatar'], $avatars)) {
    $selectedAvatar = $_POST['selected_avatar'];

    // Mise à jour en base
    $stmt = $pdo->prepare("UPDATE Utilisateur SET avatar = ? WHERE id_utilisateur = ?");
    $stmt->execute([$selectedAvatar, $userId]);

    // Redirection vers la page privée
    header("Location: private.php");
    exit;
}

$title = "Choix de l'avatar";
$description = "Page destinée au choix de la photo de profil";
$h1 = "Modification du profil";

require "./include/header.inc.php";
?>

<section class="avatar-section">
    <h2>Veuillez choisir un avatar pour votre profil</h2>

    <form method="POST" class="avatar-form">
        <div class="avatar-grid">
            <?php foreach ($avatars as $img): ?>
                <label class="avatar-option">
                    <input 
                        type="radio" 
                        name="selected_avatar" 
                        value="<?= htmlspecialchars($img) ?>"
                        <?= ($currentAvatar === $img) ? 'checked' : '' ?>
                    >
                    <img src="./images/avatars/<?= htmlspecialchars($img) ?>" alt="Avatar <?= htmlspecialchars($img) ?>">
                </label>
            <?php endforeach; ?>
        </div>

        <button class="btn-save">Enregistrer</button>
    </form>
</section>

<?php
require "./include/footer.inc.php";
?>
