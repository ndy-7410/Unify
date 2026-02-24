<?php
session_start();
require 'database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Récupérer les infos de l'utilisateur
$stmt = $db->prepare("SELECT name, email, created_at FROM user WHERE user_id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Mettre à jour le profil
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $updateStmt = $db->prepare("UPDATE user SET name = :name, email = :email WHERE user_id = :id");
    $updateStmt->execute([
        'name' => $name,
        'email' => $email,
        'id' => $user_id
    ]);

    $successMessage = "Profil mis à jour avec succès !";
    $user['name'] = $name;
    $user['email'] = $email;
}

// Supprimer le profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_profile'])) {
    $deleteStmt = $db->prepare("DELETE FROM user WHERE user_id = :id");
    $deleteStmt->execute(['id' => $user_id]);
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Unify | Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="text-center mb-4">
            <h1>Mon Profil</h1>
            <p class="text-muted">Gérez vos informations personnelles ici</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" class="card p-4 shadow-sm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compte créé le</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['created_at']) ?>"
                            disabled>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="update_profile" class="btn btn-dark">Mettre à jour</button>
                        <button type="submit" name="delete_profile" class="btn btn-danger"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre profil ? Cette action est irréversible.')">Supprimer
                            le profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>