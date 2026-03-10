<?php
// 1. Démarrage session et inclusion DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';

// --- FONCTION POUR GÉNÉRER LE HTML DES NOTIFICATIONS (AJAX + CHARGEMENT INITIAL) ---
if (!function_exists('renderNotificationsHTML')) {
    function renderNotificationsHTML($pendingInvites, $ownerNotifications, $totalNotifs) {
        ob_start();
        ?>
        <?php if (count($pendingInvites) > 0): ?>
            <li>
                <h6 class="dropdown-header text-primary fw-bold">Invitations reçues</h6>
            </li>
            <?php foreach ($pendingInvites as $inv): ?>
                <li class="dropdown-item py-2 border-bottom text-wrap w-100">
                    <form method="POST">
                        <input type="hidden" name="invitation_id" value="<?= $inv['invitation_id'] ?>">
                        <input type="hidden" name="handle_notification" value="1">
                        <p class="mb-2 small lh-sm">
                            <strong><?= htmlspecialchars($inv['inviter_name']) ?></strong> vous invite sur <br>
                            <em class="text-dark"><?= htmlspecialchars($inv['project_name']) ?></em>
                        </p>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" name="action" value="refuse" class="btn btn-sm btn-outline-danger rounded-pill px-3">Refuser</button>
                            <button type="submit" name="action" value="accept" class="btn btn-sm btn-success rounded-pill px-3">Accepter</button>
                        </div>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (count($ownerNotifications) > 0): ?>
            <li>
                <h6 class="dropdown-header text-dark fw-bold mt-2">Réponses de l'équipe</h6>
            </li>
            <?php foreach ($ownerNotifications as $notif): ?>
                <li class="dropdown-item py-2 border-bottom bg-light text-wrap w-100">
                    <form method="POST">
                        <input type="hidden" name="invitation_id" value="<?= $notif['invitation_id'] ?>">
                        <input type="hidden" name="handle_notification" value="1">
                        <p class="mb-1 small lh-sm">
                            <strong><?= htmlspecialchars($notif['email']) ?></strong> a 
                            <?php if ($notif['status'] == 'accepted'): ?>
                                <span class="text-success fw-bold">accepté</span>
                            <?php else: ?>
                                <span class="text-danger fw-bold">refusé</span>
                            <?php endif; ?>
                            pour <em><?= htmlspecialchars($notif['project_name']) ?></em>.
                        </p>
                        <div class="d-flex justify-content-end mt-1">
                            <button type="submit" name="action" value="dismiss" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none">
                                Marquer lu
                            </button>
                        </div>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($totalNotifs === 0): ?>
            <li>
                <div class="dropdown-item text-muted text-center py-4 bg-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bell-slash mb-2 opacity-50" viewBox="0 0 16 16"><path d="M5.164 14H15c-.299-.199-.557-.553-.78-1-.9-1.8-1.22-5.12-1.22-6 0-.264-.02-.523-.06-.776l-.938.938c.02.708.157 2.154.457 3.58.161.767.377 1.566.663 2.258H6.164l-1 1zm5.581-9.91a3.975 3.975 0 0 0-1.948-1.01L8 2.917l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.01.033-.021.065-.031.098l-2.01 2.01c-.196-.27-.371-.55-.523-.837C.68 10.2 1 6.88 1 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0c.942.19 1.788.645 2.457 1.284zM10 15a2 2 0 1 1-4 0h4zm-9.375.625a.53.53 0 0 0 .75.75l14.75-14.75a.53.53 0 0 0-.75-.75L.625 15.625z"/></svg>
                    <p class="mb-0 small">Aucune notification</p>
                </div>
            </li>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}

// Détection de la page courante pour mettre le lien en "actif"
$currentPage = basename($_SERVER['PHP_SELF']);

$currentUserEmail = '';
$pendingInvites = [];       
$ownerNotifications = [];   
$totalNotifs = 0;

if (isset($_SESSION["user_id"])) {
    $currentUserId = $_SESSION["user_id"];

    $stmtEmail = $pdo->prepare("SELECT email FROM user WHERE user_id = ?");
    $stmtEmail->execute([$currentUserId]);
    $currentUserEmail = $stmtEmail->fetchColumn();

    // B. TRAITEMENT DES ACTIONS (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['handle_notification'])) {
        $invit_id = intval($_POST['invitation_id']);
        $action = $_POST['action'];

        if ($action === 'accept' || $action === 'refuse') {
            $stmtVerif = $pdo->prepare("SELECT * FROM project_invitation WHERE invitation_id = ? AND email = ? AND status = 'pending'");
            $stmtVerif->execute([$invit_id, $currentUserEmail]);
            $invit = $stmtVerif->fetch(PDO::FETCH_ASSOC);

            if ($invit) {
                if ($action === 'accept') {
                    $pdo->beginTransaction();
                    try {
                        $stmtUpd = $pdo->prepare("UPDATE project_invitation SET status = 'accepted' WHERE invitation_id = ?");
                        $stmtUpd->execute([$invit_id]);

                        $stmtAdd = $pdo->prepare("INSERT IGNORE INTO project_user (project_id, user_id, role, added_at) VALUES (?, ?, 'member', NOW())");
                        $stmtAdd->execute([$invit['project_id'], $currentUserId]);

                        $pdo->commit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                    }
                } elseif ($action === 'refuse') {
                    $stmtUpd = $pdo->prepare("UPDATE project_invitation SET status = 'refused' WHERE invitation_id = ?");
                    $stmtUpd->execute([$invit_id]);
                }
            }
        } elseif ($action === 'dismiss') {
            $stmtVerifOwner = $pdo->prepare("SELECT i.invitation_id 
                                             FROM project_invitation i 
                                             JOIN project p ON i.project_id = p.project_id 
                                             WHERE i.invitation_id = ? AND p.user_id = ?");
            $stmtVerifOwner->execute([$invit_id, $currentUserId]);

            if ($stmtVerifOwner->fetch()) {
                $stmtDel = $pdo->prepare("DELETE FROM project_invitation WHERE invitation_id = ?");
                $stmtDel->execute([$invit_id]);
            }
        }

        // JS pour empêcher l'erreur PHP
        echo "<script>window.location.replace(window.location.href);</script>";
        exit();
    }

    // C. RÉCUPÉRATION DES DONNÉES
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

    $sqlOwnerNotif = "SELECT i.invitation_id, i.email, i.status, p.name as project_name
                      FROM project_invitation i
                      JOIN project p ON i.project_id = p.project_id
                      WHERE p.user_id = ? AND i.status IN ('accepted', 'refused')";
    $stmtOwnerNotif = $pdo->prepare($sqlOwnerNotif);
    $stmtOwnerNotif->execute([$currentUserId]);
    $ownerNotifications = $stmtOwnerNotif->fetchAll(PDO::FETCH_ASSOC);

    $totalNotifs = count($pendingInvites) + count($ownerNotifications);
}

// --- SYSTÈME AJAX (Vérification en arrière-plan) ---
if (isset($_GET['ajax_notif'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(['count' => 0, 'html' => '']);
        exit();
    }
    $html = renderNotificationsHTML($pendingInvites, $ownerNotifications, $totalNotifs);
    echo json_encode(['count' => $totalNotifs, 'html' => $html]);
    exit();
}
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top py-2">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="index.php">
            <img src="logo.png" alt="Logo Unify" width="30" height="24" class="d-inline-block align-text-top">
            Unify
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark <?= $currentPage == 'index.php' ? 'active fw-bold' : '' ?>" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark <?= $currentPage == 'price.php' ? 'active fw-bold' : '' ?>" href="price.php">Prix</a>
                </li>
                <?php if (isset($_SESSION["user_id"])) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?= ($currentPage == 'project.php' || $currentPage == 'task.php') ? 'active fw-bold' : '' ?>" href="project.php">Projets</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link text-dark <?= $currentPage == 'contact.php' ? 'active fw-bold' : '' ?>" href="contact.php">Contacter</a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-center gap-3">
                <?php if (isset($_SESSION["user_id"])) { ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative d-flex align-items-center text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                            <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white mt-2 ms-n2 <?= $totalNotifs > 0 ? '' : 'd-none' ?>" style="font-size: 0.65rem;">
                                <?= $totalNotifs ?>
                                <span class="visually-hidden">Alertes</span>
                            </span>
                        </a>
                        
                        <ul id="notification-list" class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 overflow-y-auto" style="width: 320px; max-height: 400px;">
                            <?= renderNotificationsHTML($pendingInvites, $ownerNotifications, $totalNotifs) ?>
                        </ul>
                    </li>

                    <li class="nav-item dropdown ms-1">
                        <a class="nav-link dropdown-toggle d-flex align-items-center py-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center border border-2 border-white shadow-sm fw-bold" style="width: 36px; height: 36px;">
                                <?= isset($_SESSION["name"]) ? strtoupper(substr($_SESSION["name"], 0, 1)) : 'U' ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><h6 class="dropdown-header text-dark fw-bold">Bonjour, <?= isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : 'Utilisateur' ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="project.php">Mes projets</a></li>
                            <li><a class="dropdown-item" href="profile.php">Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger fw-bold" href="logout.php">Se déconnecter</a></li>
                        </ul>
                    </li>

                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-dark rounded-pill px-4 ms-2" href="register.php">S'inscrire</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if (isset($_SESSION["user_id"])): ?>
    // Vérifie les notifications toutes les 5 secondes
    setInterval(function() {
        fetch('navbar.php?ajax_notif=1')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('notification-list');
            if (list) {
                list.innerHTML = data.html;
            }
            
            const badge = document.getElementById('notif-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.classList.remove('d-none');
                    badge.innerHTML = data.count + '<span class="visually-hidden">Alertes</span>';
                } else {
                    badge.classList.add('d-none');
                }
            }
        })
        .catch(err => console.error("Erreur actualisation notifs:", err));
    }, 5000);
    <?php endif; ?>
});
</script>