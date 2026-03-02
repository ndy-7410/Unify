<!DOCTYPE html>
<html lang="fr">
<?php
// 1. Démarrage session et inclusion DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';

// Variables pour stocker les notifications
$currentUserEmail = '';
$pendingInvites = [];       // Invitations que JE reçois
$ownerNotifications = [];   // Réponses des gens que J'AI invités

if (isset($_SESSION["user_id"])) {
    $currentUserId = $_SESSION["user_id"];

    // A. Récupérer l'email de l'utilisateur connecté
    $stmtEmail = $pdo->prepare("SELECT email FROM user WHERE user_id = ?");
    $stmtEmail->execute([$currentUserId]);
    $currentUserEmail = $stmtEmail->fetchColumn();

    // B. TRAITEMENT DES ACTIONS (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['handle_notification'])) {
        $invit_id = intval($_POST['invitation_id']);
        $action = $_POST['action'];

        // Cas 1 : Je suis l'INVITÉ et je réponds (Accepter/Refuser)
        if ($action === 'accept' || $action === 'refuse') {
            // Vérif : c'est bien mon email
            $stmtVerif = $pdo->prepare("SELECT * FROM project_invitation WHERE invitation_id = ? AND email = ? AND status = 'pending'");
            $stmtVerif->execute([$invit_id, $currentUserEmail]);
            $invit = $stmtVerif->fetch(PDO::FETCH_ASSOC);

            if ($invit) {
                if ($action === 'accept') {
                    $pdo->beginTransaction();
                    try {
                        // 1. Mise à jour statut invitation (pour notifier le proprio)
                        $stmtUpd = $pdo->prepare("UPDATE project_invitation SET status = 'accepted' WHERE invitation_id = ?");
                        $stmtUpd->execute([$invit_id]);

                        // 2. Ajout dans l'équipe
                        $stmtAdd = $pdo->prepare("INSERT IGNORE INTO project_user (project_id, user_id, role, added_at) VALUES (?, ?, 'member', NOW())");
                        $stmtAdd->execute([$invit['project_id'], $currentUserId]);

                        $pdo->commit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                    }
                } elseif ($action === 'refuse') {
                    // Juste mise à jour statut (pour notifier le proprio)
                    $stmtUpd = $pdo->prepare("UPDATE project_invitation SET status = 'refused' WHERE invitation_id = ?");
                    $stmtUpd->execute([$invit_id]);
                }
            }
        }

        // Cas 2 : Je suis le PROPRIÉTAIRE et je "Note" la réponse (Suppression de la notif)
        elseif ($action === 'dismiss') {
            // Vérif : le projet m'appartient
            $stmtVerifOwner = $pdo->prepare("SELECT i.invitation_id 
                                             FROM project_invitation i 
                                             JOIN project p ON i.project_id = p.project_id 
                                             WHERE i.invitation_id = ? AND p.user_id = ?");
            $stmtVerifOwner->execute([$invit_id, $currentUserId]);

            if ($stmtVerifOwner->fetch()) {
                // On supprime l'invitation car l'info a été vue et traitée
                // (Si accepté, l'user est déjà dans project_user, donc on peut supprimer l'invit sans risque)
                $stmtDel = $pdo->prepare("DELETE FROM project_invitation WHERE invitation_id = ?");
                $stmtDel->execute([$invit_id]);
            }
        }

        // Refresh pour effacer la notif visuellement
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    // C. RÉCUPÉRATION DES DONNÉES

    // 1. Invitations en attente (POUR MOI)
    if ($currentUserEmail) {
        $sqlInvites = "SELECT i.invitation_id, p.name as project_name, u.name as inviter_name
                       FROM project_invitation i
                       JOIN project p ON i.project_id = p.project_id
                       JOIN user u ON p.user_id = u.user_id
                       WHERE i.email = ? AND i.status = 'pending'";
        $stmtInvites = $pdo->prepare($sqlInvites);
        $stmtInvites->execute([$currentUserEmail]);
        $pendingInvites = $stmtInvites->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Réponses aux invitations (DE MES PROJETS)
    // On cherche les invitations sur MES projets qui sont 'accepted' ou 'refused'
    $sqlOwnerNotif = "SELECT i.invitation_id, i.email, i.status, p.name as project_name
                      FROM project_invitation i
                      JOIN project p ON i.project_id = p.project_id
                      WHERE p.user_id = ? AND i.status IN ('accepted', 'refused')";
    $stmtOwnerNotif = $pdo->prepare($sqlOwnerNotif);
    $stmtOwnerNotif->execute([$currentUserId]);
    $ownerNotifications = $stmtOwnerNotif->fetchAll(PDO::FETCH_ASSOC);

    // Total des notifs
    $totalNotifs = count($pendingInvites) + count($ownerNotifications);
}
?>

<head>
    <meta charset="UTF-8" />
    <title>Unify | Barre de navigation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">

            <a class="navbar-brand" href="index.php">
                <img src="logo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
            </a>
            <a class="navbar-brand" href="index.php">Unify</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="price.php">Prix</a>
                    </li>

                    <?php if (isset($_SESSION["user_id"])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="project.php">Projet</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contacter</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION["user_id"])) { ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                                </svg>
                                <?php if ($totalNotifs > 0): ?>
                                    <span class="position-absolute top-10 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">Alertes</span>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-height: 400px; overflow-y: auto;">

                                <?php if (count($pendingInvites) > 0): ?>
                                    <li>
                                        <h6 class="dropdown-header">Invitations reçues</h6>
                                    </li>
                                    <?php foreach ($pendingInvites as $inv): ?>
                                        <li class="p-2 border-bottom">
                                            <form method="POST">
                                                <input type="hidden" name="invitation_id" value="<?= $inv['invitation_id'] ?>">
                                                <input type="hidden" name="handle_notification" value="1">

                                                <p class="mb-2 small text-wrap">
                                                    <strong><?= htmlspecialchars($inv['inviter_name']) ?></strong> vous invite à rejoindre <br>
                                                    <em><?= htmlspecialchars($inv['project_name']) ?></em>
                                                </p>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="submit" name="action" value="accept" class="btn btn-sm btn-success py-0" style="font-size: 0.8rem;">Accepter</button>
                                                    <button type="submit" name="action" value="refuse" class="btn btn-sm btn-outline-danger py-0" style="font-size: 0.8rem;">Refuser</button>
                                                </div>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (count($ownerNotifications) > 0): ?>
                                    <li>
                                        <h6 class="dropdown-header">Réponses collaborateurs</h6>
                                    </li>
                                    <?php foreach ($ownerNotifications as $notif): ?>
                                        <li class="p-2 border-bottom bg-light">
                                            <form method="POST">
                                                <input type="hidden" name="invitation_id" value="<?= $notif['invitation_id'] ?>">
                                                <input type="hidden" name="handle_notification" value="1">

                                                <p class="mb-1 small text-wrap">
                                                    L'utilisateur <strong><?= htmlspecialchars($notif['email']) ?></strong> a
                                                    <?php if ($notif['status'] == 'accepted'): ?>
                                                        <span class="text-success fw-bold">accepté</span>
                                                    <?php else: ?>
                                                        <span class="text-danger fw-bold">refusé</span>
                                                    <?php endif; ?>
                                                    votre invitation pour <em><?= htmlspecialchars($notif['project_name']) ?></em>.
                                                </p>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" name="action" value="dismiss" class="btn btn-sm btn-link text-muted py-0" style="text-decoration: none; font-size: 0.8rem;">
                                                        Marquer comme lu
                                                    </button>
                                                </div>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if ($totalNotifs === 0): ?>
                                    <li><span class="dropdown-item text-muted text-center py-3">Aucune notification</span></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                </svg>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="project.php">Voir mes projet</a></li>
                                <li><a class="dropdown-item" href="profile.php">Voir mon profil</a></li>
                                <li><a class="dropdown-item disabled">Connecté : <?= isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : '' ?></a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout.php">Se déconnecter</a></li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se connecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">S'inscrire</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>