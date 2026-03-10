<?php
require 'database.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    die("ID de projet invalide !");
}

$project_id = intval($_GET['project_id']);

// -------------------------------------------------------------------------
// 1. RÉCUPÉRATION PROJET
// -------------------------------------------------------------------------
$sqlProject = "SELECT p.project_id, p.name, p.description, p.user_id AS owner_id, u.name AS owner_name 
               FROM project p
               JOIN user u ON p.user_id = u.user_id 
               LEFT JOIN project_user pu ON p.project_id = pu.project_id 
               WHERE p.project_id = :project_id 
               AND (p.user_id = :user_id OR pu.user_id = :user_id)";

$stmtProject = $pdo->prepare($sqlProject);
$stmtProject->execute(['project_id' => $project_id, 'user_id' => $user_id]);
$project = $stmtProject->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Projet introuvable ou vous n'avez pas les droits pour y accéder !");
}

$is_owner = ($project['owner_id'] == $user_id);

// --- CORRECTION DU TRI ICI ---
$sort_column = 't.created_at';
$sort_order = 'ASC';
$valid_columns = ['status', 'priority', 'deadline'];
$current_sort = '';

if (isset($_GET['sort']) && in_array($_GET['sort'], $valid_columns)) {
    $current_sort = $_GET['sort'];
    if ($_GET['sort'] == 'status') {
        $sort_column = 't.status_id';
    } elseif ($_GET['sort'] == 'priority') {
        $sort_column = 't.priority_id';
    } elseif ($_GET['sort'] == 'deadline') {
        $sort_column = 't.deadline';
    }
}

if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'])) {
    $sort_order = strtoupper($_GET['order']);
}

// --- TRAITEMENT AJAX (AUTO-SAVE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update'])) {
    header('Content-Type: application/json');

    $task_id = intval($_POST['task_id']);
    $field = $_POST['field'];
    $value = trim($_POST['value']);

    $allowed_fields = ['name', 'description', 'status_id', 'priority_id', 'deadline'];

    if (in_array($field, $allowed_fields)) {

        if ($field === 'deadline' && empty($value)) {
            $value = null;
        }

        $sql = "UPDATE task SET $field = :value, updated_at = NOW() WHERE task_id = :task_id AND project_id = :project_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'value' => $value,
            'task_id' => $task_id,
            'project_id' => $project_id
        ]);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Enregistré']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur SQL']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Champ invalide']);
    }
    exit;
}

// --- TRAITEMENT CLASSIQUE ---
$member_error = '';
$member_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Création Tâche
    if (isset($_POST['create_task'])) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status_id = intval($_POST['status_id'] ?? 1);
        $priority_id = intval($_POST['priority_id'] ?? 1);
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

        if ($name != '') {
            $stmt = $pdo->prepare("INSERT INTO task(project_id,user_id,name,description,deadline,status_id,priority_id,created_at,updated_at)VALUES(?,?,?,?,?,?,?,NOW(),NOW())");
            $stmt->execute([$project_id, $user_id, $name, $description, $deadline, $status_id, $priority_id]);
            header("Location: task.php?project_id=$project_id");
            exit();
        }
    }

    // Suppression Tâche
    if (isset($_POST['delete_task'])) {
        $task_id = intval($_POST['task_id']);
        $stmt = $pdo->prepare("DELETE FROM task WHERE task_id=? AND project_id=?");
        $stmt->execute([$task_id, $project_id]);
        header("Location: task.php?project_id=$project_id");
        exit();
    }

    // Gestion Membres
    if (isset($_POST['add_member'])) {
        $email = trim($_POST['email']);
        $stmtUser = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmtUser->execute([$email]);
        $targetUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($targetUser) {
            $targetUserId = $targetUser['user_id'];

            $stmtCheckMember = $pdo->prepare("SELECT * FROM project_user WHERE project_id = ? AND user_id = ?");
            $stmtCheckMember->execute([$project_id, $targetUserId]);

            if ($targetUserId == $project['owner_id']) {
                $member_error = "Cet utilisateur est déjà le propriétaire du projet.";
            } elseif ($stmtCheckMember->fetch()) {
                $member_error = "Cet utilisateur est déjà membre du projet.";
            } else {
                $stmtCheckInvite = $pdo->prepare("SELECT * FROM project_invitation WHERE project_id = ? AND email = ? AND status = 'pending'");
                $stmtCheckInvite->execute([$project_id, $email]);

                if ($stmtCheckInvite->fetch()) {
                    $member_error = "Une invitation est déjà en attente pour cet utilisateur.";
                } else {
                    $token = bin2hex(random_bytes(16));
                    $stmtInvite = $pdo->prepare("INSERT INTO project_invitation (project_id, email, token, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
                    $stmtInvite->execute([$project_id, $email, $token]);
                    $member_success = "L'invitation a été envoyée à $email avec succès !";
                }
            }
        } else {
            $member_error = "Aucun compte Unify trouvé avec cette adresse email.";
        }
    }

    // Suppression / Quitter
    if (isset($_POST['remove_member'])) {
        $target_user_id = intval($_POST['target_user_id']);
        if ($is_owner || $target_user_id == $user_id) {
            $stmt = $pdo->prepare("DELETE FROM project_user WHERE project_id=? AND user_id=?");
            $stmt->execute([$project_id, $target_user_id]);
            if ($target_user_id == $user_id) {
                header("Location: project.php");
                exit;
            }
        }
        header("Location: task.php?project_id=$project_id");
        exit();
    }
}

// Récupération des données avec le tri
$sqlTasks = "SELECT t.task_id, t.name, t.description, t.deadline, t.created_at, t.updated_at, 
            t.status_id, t.priority_id, u.name as creator_name
          FROM task t
          JOIN user u ON t.user_id = u.user_id
          WHERE t.project_id=:project_id";

// Modification pour que les "deadline" vides aillent à la fin si on trie par échéance
if ($sort_column === 't.deadline' && $sort_order === 'ASC') {
    $sqlTasks .= " ORDER BY t.deadline IS NULL, t.deadline ASC";
} else {
    $sqlTasks .= " ORDER BY $sort_column $sort_order";
}

$stmtTasks = $pdo->prepare($sqlTasks);
$stmtTasks->execute(['project_id' => $project_id]);
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

$sqlMembers = "SELECT u.user_id, u.name, u.email FROM project_user pu JOIN user u ON pu.user_id = u.user_id WHERE pu.project_id = ?";
$stmtMembers = $pdo->prepare($sqlMembers);
$stmtMembers->execute([$project_id]);
$members = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Unify | Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .creator-badge {
            font-size: 0.75rem;
            color: #99a2aa;
            display: block;
            margin-top: 4px;
        }

        /* CHIPS UTILISATEURS */
        .user-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px 4px 4px;
            border: 2px solid;
            border-radius: 50px;
            background-color: #fff;
            transition: all 0.2s;
        }

        .user-chip .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 10px;
        }

        .user-chip.owner {
            border-color: #000;
            color: #000;
        }

        .user-chip.owner .avatar-circle {
            background-color: #000;
            color: #fff;
        }

        .user-chip.collab {
            border-color: #dee2e6;
            color: #6c757d;
        }

        .user-chip.collab .avatar-circle {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .btn-action-rounded {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-remove {
            background: #fee2e2;
            color: #dc2626;
            margin-left: 8px;
        }

        .btn-remove:hover {
            background: #dc2626;
            color: white;
        }

        .btn-quit {
            background-color: #dc3545;
            color: white;
            border-radius: 20px;
            font-size: 0.7rem;
            padding: 4px 10px;
            margin-left: 8px;
            text-decoration: none;
            font-weight: 600;
            border: none;
        }

        .btn-quit:hover {
            background-color: #b02a37;
        }

        .btn-add-member {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #212529;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .btn-add-member:hover {
            transform: scale(1.1);
        }

        /* Inputs transparents */
        .form-control-transparent {
            border: 1px solid transparent;
            background: transparent;
            padding: 0.375rem 0.5rem;
            transition: 0.2s;
            border-radius: 6px;
        }

        .form-control-transparent:hover,
        .form-control-transparent:focus {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        /* ALIGNEMENT TABLEAU */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .table tbody td {
            vertical-align: top !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        /* --- STYLE MENU DÉROULANT (PILULE) --- */
        .select-minimal {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            border: 1px solid #e9ecef;
            background-color: #f8f9fa;

            font-weight: 500;
            color: #495057;
            cursor: pointer;
            text-align: center;
            text-align-last: center;

            border-radius: 50px;
            padding: 0.25rem 2rem 0.25rem 1rem;

            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);

            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m4 6 4 4 4-4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 10px;
        }

        .select-minimal:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #212529;
        }

        .select-minimal:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        input[type="date"].form-control-transparent::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            transition: 0.2s;
        }

        input[type="date"].form-control-transparent:hover::-webkit-calendar-picker-indicator {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-white d-flex flex-column min-vh-100">

    <?php include 'navbar.php'; ?>

    <section class="py-5 container">
        <div class="row py-lg-3">
            <div class="col-lg-10 mx-auto">
                <div class="text-center mb-4">
                    <h1 class="fw-bold mb-3"><?= htmlspecialchars($project['name']) ?></h1>
                    <p class="text-muted mb-4 lead"><?= htmlspecialchars($project['description']) ?></p>

                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                        <div class="user-chip owner" title="Propriétaire">
                            <div class="avatar-circle"><?= strtoupper(substr($project['owner_name'], 0, 1)) ?></div>
                            <span class="fw-bold"><?= htmlspecialchars($project['owner_name']) ?></span>
                            <?php if ($is_owner): ?><span class="ms-1 small text-muted">(Moi)</span><?php endif; ?>
                        </div>
                        <?php foreach ($members as $m): ?>
                            <div class="user-chip collab">
                                <div class="avatar-circle"><?= strtoupper(substr($m['name'], 0, 1)) ?></div>
                                <span class="fw-medium"><?= htmlspecialchars($m['name']) ?></span>
                                <?php if ($is_owner): ?>
                                    <form method="POST" onsubmit="return confirm('Retirer ce membre ?');" class="d-inline">
                                        <input type="hidden" name="target_user_id" value="<?= $m['user_id'] ?>">
                                        <button type="submit" name="remove_member" class="btn-action-rounded btn-remove">&times;</button>
                                    </form>
                                <?php elseif ($m['user_id'] == $user_id): ?>
                                    <form method="POST" onsubmit="return confirm('Quitter le projet ?');" class="d-inline">
                                        <input type="hidden" name="target_user_id" value="<?= $m['user_id'] ?>">
                                        <button type="submit" name="remove_member" class="btn-quit">Quitter</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($is_owner): ?>
                            <button class="btn-add-member shadow-sm" id="showAddMember" title="Inviter">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($member_error): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center mx-auto" style="max-width: 600px;">
                        <?= htmlspecialchars($member_error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($member_success): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm text-center mx-auto" style="max-width: 600px;">
                        <?= htmlspecialchars($member_success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <button class="btn btn-dark px-4 py-2 rounded-pill" id="showCreateTask">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Nouvelle tâche
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5 flex-grow-1">

        <div class="d-flex justify-content-end mb-3 align-items-center small text-muted">
            <span class="me-2">Trier par :</span>
            <div class="btn-group btn-group-sm">
                <a href="?project_id=<?= $project_id ?>&sort=deadline&order=ASC" class="btn <?= $current_sort == 'deadline' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Échéance</a>
                <a href="?project_id=<?= $project_id ?>&sort=status&order=ASC" class="btn <?= $current_sort == 'status' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Statut</a>
                <a href="?project_id=<?= $project_id ?>&sort=priority&order=DESC" class="btn <?= $current_sort == 'priority' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Priorité</a>
                <?php if ($current_sort): ?>
                    <a href="?project_id=<?= $project_id ?>" class="btn btn-outline-danger" title="Annuler le tri">&times;</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive rounded-3 border shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20%;">Tâche</th>
                        <th style="width: 25%;">Description</th>
                        <th style="width: 15%;" class="text-center">Échéance</th>
                        <th style="width: 15%;" class="text-center">Statut</th>
                        <th style="width: 15%;" class="text-center">Priorité</th>
                        <th style="width: 10%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if ($tasks): foreach ($tasks as $task): ?>
                            <tr data-task-id="<?= $task['task_id'] ?>">
                                <td>
                                    <input type="text" class="form-control form-control-transparent fw-semibold task-input"
                                        data-field="name" value="<?= htmlspecialchars($task['name']) ?>">
                                    <span class="creator-badge">
                                        Par <?= htmlspecialchars($task['creator_name']) ?>, <?= date('d/m', strtotime($task['created_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-transparent task-input"
                                        data-field="description" value="<?= htmlspecialchars($task['description']) ?>">
                                </td>

                                <td class="text-center">
                                    <input type="date" class="form-control form-control-sm form-control-transparent task-input mx-auto text-center"
                                        style="max-width: 130px; font-size: 0.85rem; font-weight: 500;"
                                        data-field="deadline" value="<?= htmlspecialchars($task['deadline'] ?? '') ?>">
                                </td>

                                <td class="text-center">
                                    <select class="form-select form-select-sm select-minimal task-input mx-auto" data-field="status_id">
                                        <option value="1" <?= $task['status_id'] == 1 ? 'selected' : '' ?>>⚪ À faire</option>
                                        <option value="2" <?= $task['status_id'] == 2 ? 'selected' : '' ?>>🔵 En cours</option>
                                        <option value="3" <?= $task['status_id'] == 3 ? 'selected' : '' ?>>🟢 Terminé</option>
                                        <option value="4" <?= $task['status_id'] == 4 ? 'selected' : '' ?>>🟣 En test</option>
                                        <option value="5" <?= $task['status_id'] == 5 ? 'selected' : '' ?>>🔴 Bloqué</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-select form-select-sm select-minimal task-input mx-auto" data-field="priority_id">
                                        <option value="1" <?= $task['priority_id'] == 1 ? 'selected' : '' ?>>⚪ Minimum</option>
                                        <option value="2" <?= $task['priority_id'] == 2 ? 'selected' : '' ?>>🔵 Moyen</option>
                                        <option value="3" <?= $task['priority_id'] == 3 ? 'selected' : '' ?>>🟠 Important</option>
                                        <option value="4" <?= $task['priority_id'] == 4 ? 'selected' : '' ?>>🔴 Urgent</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <form method="POST" onsubmit="return confirm('Supprimer cette tâche ?');" class="d-inline-block mt-1">
                                        <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                        <button type="submit" name="delete_task" class="btn btn-outline-danger btn-sm border-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Aucune tâche.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1055;">
        <div id="saveToast" class="toast align-items-center border-0 shadow-lg rounded-pill px-2 py-1" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center fw-medium text-dark" style="font-size: 0.95rem;">
                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 28px; height: 28px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-check" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                        </svg>
                    </div>
                    Modifications enregistrées
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Nouvelle tâche</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body pt-4">
                        <label class="form-label fw-semibold small text-muted">TITRE</label>
                        <input type="text" name="name" class="form-control mb-3" required>

                        <label class="form-label fw-semibold small text-muted">DESCRIPTION</label>
                        <textarea name="description" class="form-control mb-3" rows="2"></textarea>

                        <label class="form-label fw-semibold small text-muted">ÉCHÉANCE (Optionnel)</label>
                        <input type="date" name="deadline" class="form-control mb-3">

                        <div class="row">
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-muted">STATUT</label>
                                <select name="status_id" class="form-select mb-3">
                                    <option value="1">⚪ À faire</option>
                                    <option value="2">🔵 En cours</option>
                                    <option value="3">🟢 Terminé</option>
                                    <option value="4">🟣 En test</option>
                                    <option value="5">🔴 Bloqué</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-muted">PRIORITÉ</label>
                                <select name="priority_id" class="form-select mb-3">
                                    <option value="1">⚪ Minimum</option>
                                    <option value="2">🔵 Moyen</option>
                                    <option value="3">🟠 Important</option>
                                    <option value="4">🔴 Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4">
                        <button class="btn btn-link text-decoration-none text-muted" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="create_task" class="btn btn-dark px-4">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Inviter un collaborateur</h5><button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body pt-4"><label class="form-label fw-semibold small text-muted">EMAIL</label><input type="email" name="email" class="form-control mb-3" placeholder="exemple@mail.com" required></div>
                    <div class="modal-footer border-top-0 pt-0 pb-4"><button class="btn btn-link text-decoration-none text-muted" data-bs-dismiss="modal">Annuler</button><button type="submit" name="add_member" class="btn btn-dark px-4">Envoyer l'invitation</button></div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.getElementById('showCreateTask').onclick = () => new bootstrap.Modal('#createTaskModal').show();
        const addMemberBtn = document.getElementById('showAddMember');
        if (addMemberBtn) addMemberBtn.onclick = () => new bootstrap.Modal('#addMemberModal').show();

        const saveToast = new bootstrap.Toast(document.getElementById('saveToast'));
        const projectId = <?= $project_id ?>;

        document.querySelectorAll('.task-input').forEach(input => {
            input.addEventListener('change', function() {
                saveData(this);
            });
        });

        function saveData(element) {
            const row = element.closest('tr');
            const taskId = row.dataset.taskId;
            const field = element.dataset.field;
            const value = element.value;

            const formData = new FormData();
            formData.append('ajax_update', '1');
            formData.append('task_id', taskId);
            formData.append('field', field);
            formData.append('value', value);

            fetch('task.php?project_id=' + projectId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') saveToast.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>