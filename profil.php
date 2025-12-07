<?php
session_start();

// Liste des avatars
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

$currentAvatar = $_SESSION['avatar'] ?? "default-avatar.png";

// Enregistrement + Redirection
if (isset($_POST['selected_avatar'])) {
    $_SESSION['avatar'] = $_POST['selected_avatar'];
    header("Location: private.php"); // ⬅️ Redirection immédiate
    exit;
}



$title = "Choix de l'avatar";
$description = "page destinée au choix de la photo de profil";
$h1 = "Modification du profil";

require "./include/header.inc.php";
?>

<section class="avatar-section">
    <h2> Veuillez choisir un avatar pour votre profil</h2>
    <?php if (!empty($message)) : ?>
        <p class="avatar-message">
            <?= $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="avatar-form">
        <div class="avatar-grid">
            <?php foreach ($avatars as $img): ?>
                <label class="avatar-option">
                    <input 
                        type="radio" 
                        name="selected_avatar" 
                        value="<?= $img ?>"
                        <?= ($currentAvatar === $img) ? 'checked' : '' ?>
                    >
                    <img src="./images/avatars/<?= $img ?>" alt="Avatar">
                </label>
            <?php endforeach; ?>
        </div>

        <button class="btn-save">Enregistrer</button>
    </form>

</section>

<?php
require "./include/footer.inc.php";
?>
