<?php
session_start();
require 'database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Vérifier quel plan il veut acheter
$plan_choisi = $_GET['plan'] ?? 'pro';
$prix = ($plan_choisi == 'business') ? '15,00 €' : '5,00 €';
$nom_plan = ($plan_choisi == 'business') ? 'Forfait Business' : 'Forfait Pro';

// Si le formulaire de paiement est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {
    
    // 1. On simule le traitement (dans la vraie vie, on interrogerait la banque ici)
    $user_id = $_SESSION["user_id"];
    
    // 2. On met à jour la base de données directement !
    $stmt = $pdo->prepare("UPDATE user SET plan = ? WHERE user_id = ?");
    $stmt->execute([$plan_choisi, $user_id]);
    
    // 3. On redirige vers le profil
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Paiement Sécurisé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .checkout-box { max-width: 450px; margin: 0 auto; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 py-5">

    <div class="container">
        <div class="text-center mb-4">
            <img src="logo.png" alt="Unify Logo" height="40" class="mb-3">
            <h2 class="fw-bold">Finaliser la commande</h2>
            <p class="text-muted">Vous êtes sur le point d'acheter le <strong><?= $nom_plan ?></strong></p>
        </div>

        <div class="card shadow-sm border-0 rounded-4 checkout-box">
            <div class="card-body p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <span class="fs-5 text-secondary">Total à payer :</span>
                    <span class="fs-3 fw-bold text-dark"><?= $prix ?></span>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">NOM SUR LA CARTE</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">NUMÉRO DE CARTE</label>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">EXPIRATION</label>
                            <input type="text" class="form-control" placeholder="MM/AA" maxlength="5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">CVC</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small border-0 bg-info-subtle text-info-emphasis mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle me-1 mb-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
                        <strong>Note du projet :</strong> Ceci est un simulateur de paiement. Aucune vraie transaction n'est effectuée.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="simulate_payment" class="btn btn-dark btn-lg fw-bold rounded-pill shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-lock-fill me-2 mb-1" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                            Payer <?= $prix ?>
                        </button>
                        <a href="price.php" class="btn btn-link text-muted text-decoration-none mt-2">Annuler et retourner aux tarifs</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>
</html>