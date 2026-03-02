<?php
session_start();
require 'database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// --- 1. GESTION DES MESSAGES ---
$infoSuccess = "";
$passwordSuccess = "";
$passwordError = "";

// --- 2. MISE À JOUR INFOS (Nom/Email) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name && $email) {
        $updateStmt = $pdo->prepare("UPDATE user SET name = :name, email = :email WHERE user_id = :id");
        $updateStmt->execute(['name' => $name, 'email' => $email, 'id' => $user_id]);
        
        $infoSuccess = "Informations mises à jour avec succès.";
        $_SESSION['name'] = $name; // Mise à jour session
    }
}

// --- 3. MODIFICATION MOT DE PASSE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Récupérer le hash actuel
    $stmt = $pdo->prepare("SELECT password_hash FROM user WHERE user_id = :id");
    $stmt->execute(['id' => $user_id]);
    $currentHash = $stmt->fetchColumn();

    // Vérifications
    if (!password_verify($current_password, $currentHash)) {
        $passwordError = "Le mot de passe actuel est incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $passwordError = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 6) {
        $passwordError = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    } else {
        // Tout est bon, on hash et on update
        $newHash = password_hash($new_password, PASSWORD_DEFAULT);
        $updStmt = $pdo->prepare("UPDATE user SET password_hash = :hash WHERE user_id = :id");
        $updStmt->execute(['hash' => $newHash, 'id' => $user_id]);
        $passwordSuccess = "Mot de passe modifié avec succès !";
    }
}

// --- 4. SUPPRESSION COMPTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_profile'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM user WHERE user_id = :id");
    $deleteStmt->execute(['id' => $user_id]);
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- 5. RÉCUPÉRATION DONNÉES UTILISATEUR (Pour affichage) ---
$stmt = $pdo->prepare("SELECT name, email, created_at FROM user WHERE user_id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Unify | Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-white d-flex flex-column min-vh-100">
    
    <?php include 'navbar.php'; ?>

    <section class="py-5 container flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <div class="text-center mb-5">
                    <h1 class="fw-bold mb-2">Mon Profil</h1>
                    <p class="text-muted">Gérez vos informations personnelles et votre sécurité.</p>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <div class="bg-light rounded-circle p-2 me-3 text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                                    <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                                </svg>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Informations personnelles</h5>
                        </div>

                        <?php if ($infoSuccess): ?>
                            <div class="alert alert-success py-2 small"><?= htmlspecialchars($infoSuccess) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nom complet</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Adresse email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" name="update_profile" class="btn btn-dark">Enregistrer les infos</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <div class="bg-light rounded-circle p-2 me-3 text-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7 7 0 0 0 1.048-.625 11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 63 63 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/>
                                </svg>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Sécurité</h5>
                        </div>

                        <?php if ($passwordSuccess): ?>
                            <div class="alert alert-success py-2 small"><?= htmlspecialchars($passwordSuccess) ?></div>
                        <?php endif; ?>
                        <?php if ($passwordError): ?>
                            <div class="alert alert-danger py-2 small"><?= htmlspecialchars($passwordError) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mot de passe actuel</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Confirmer</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-2">
                                <button type="submit" name="update_password" class="btn btn-outline-dark">Modifier le mot de passe</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-danger-subtle bg-danger-subtle bg-opacity-10 mb-5">
                    <div class="card-body p-4">
                        <h5 class="card-title text-danger fw-bold fs-6">Zone de danger</h5>
                        <p class="card-text text-muted small mb-3">
                            La suppression de votre compte est irréversible. Toutes vos données seront effacées.
                        </p>
                        <form method="POST">
                            <button type="submit" name="delete_profile" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Êtes-vous absolument sûr ? Cette action est définitive.')">
                                Supprimer mon compte
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center text-muted small mb-5">
                    Membre depuis le <?= date("d/m/Y", strtotime($user['created_at'])) ?>
                </div>

            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>

</html>