<?php
session_start();
require 'database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Création d'un nouveau projet (AVEC VÉRIFICATION DU FORFAIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

    if ($name !== '') {
        // 1. On récupère le forfait de l'utilisateur
        $stmtPlan = $pdo->prepare("SELECT plan FROM user WHERE user_id = ?");
        $stmtPlan->execute([$user_id]);
        $userPlan = $stmtPlan->fetchColumn() ?: 'starter';

        // 2. On compte combien de projets (dont il est propriétaire) il possède déjà
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM project WHERE user_id = ?");
        $stmtCount->execute([$user_id]);
        $projectCount = $stmtCount->fetchColumn();

        // 3. On définit la limite selon le forfait
        $maxProjects = 3; // Starter
        if ($userPlan === 'pro') $maxProjects = 5;
        if ($userPlan === 'business') $maxProjects = 999999;

        // 4. On vérifie s'il a le droit
        if ($projectCount >= $maxProjects) {
            $error = "Limite atteinte. Votre forfait " . ucfirst($userPlan) . " vous limite à $maxProjects projets. Veuillez améliorer votre abonnement dans l'onglet Prix.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO project (user_id, name, description, deadline, created_at, updated_at)
                                   VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$user_id, $name, $description, $deadline]);
            header("Location: project.php");
            exit();
        }
    } else {
        $error = "Le nom du projet est obligatoire.";
    }
}

// Modification d'un projet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $project_id = $_POST['project_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

    if ($name !== '' && $project_id !== '') {
        $stmt = $pdo->prepare("UPDATE project
                               SET name = ?, description = ?, deadline = ?, updated_at = NOW()
                               WHERE project_id = ? AND user_id = ?");
        $stmt->execute([$name, $description, $deadline, $project_id, $user_id]);
        header("Location: project.php");
        exit();
    } else {
        $update_error = "Le nom du projet est obligatoire.";
    }
}

// Suppression d'un projet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'] ?? '';
    if ($project_id !== '') {
        $stmt = $pdo->prepare("DELETE FROM project WHERE project_id = ? AND user_id = ?");
        $stmt->execute([$project_id, $user_id]);
        header("Location: project.php");
        exit();
    }
}

// --- RECUPERATION DES PROJETS ---
$sql = "SELECT DISTINCT p.project_id, p.name, p.description, p.deadline, p.created_at, p.updated_at, p.user_id as owner_id, u.name as owner_name
        FROM project p
        LEFT JOIN project_user pu ON p.project_id = pu.project_id
        JOIN user u ON p.user_id = u.user_id 
        WHERE p.user_id = :user_id OR pu.user_id = :user_id
        ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Mes Projets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .card-project {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, .08);
        }

        .card-project:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1) !important;
        }
    </style>
</head>

<body class="bg-white d-flex flex-column min-vh-100">

    <?php include 'navbar.php'; ?>

    <section class="py-5 text-center container mb-4 px-3">
        <div class="row py-lg-3">
            <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-bold mb-3">Mes Projets</h1>
                <p class="text-muted mb-4">Gérez vos espaces de travail et collaborez avec votre équipe.</p>
                <button class="btn btn-dark px-4 py-2" id="showFormBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                    </svg>
                    Nouveau projet
                </button>
            </div>
        </div>
    </section>

    <div class="album pb-5 bg-white flex-grow-1 px-3 px-md-0">
        <div class="container">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center mx-auto mb-4" style="max-width: 600px;">
                    <strong>Action bloquée :</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($projects)): ?>
                <div class="text-center py-5">
                    <div class="mb-4 text-muted" style="opacity: 0.15;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                            <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-1z" />
                            <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5" />
                        </svg>
                    </div>
                    <h4 class="text-muted fw-normal">Aucun projet pour le moment</h4>
                    <p class="text-secondary small">Commencez par créer votre premier espace de travail.</p>
                </div>
            <?php else: ?>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($projects as $p):
                        $is_owner = ($p['owner_id'] == $user_id);
                    ?>
                        <div class="col">
                            <div class="card card-project h-100 shadow-sm rounded-4 border-0">
                                <div class="card-body d-flex flex-column p-4">

                                    <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                        <h5 class="card-title fw-bold text-truncate flex-grow-1" title="<?= htmlspecialchars($p['name']) ?>">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </h5>
                                        <?php if ($is_owner): ?>
                                            <span class="badge bg-dark text-white rounded-pill px-2 px-sm-3 flex-shrink-0">Propriétaire</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border rounded-pill px-2 px-sm-3 flex-shrink-0">Partagé</span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="card-text text-muted small flex-grow-1 mb-2">
                                        <?= !empty($p['description']) ? htmlspecialchars($p['description']) : '<em>Aucune description</em>' ?>
                                    </p>

                                    <?php if (!empty($p['deadline'])): ?>
                                        <div class="mb-3">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-clock me-1 mb-1" viewBox="0 0 16 16">
                                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                                </svg>
                                                Échéance : <?= date("d/m/Y", strtotime($p['deadline'])) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-secondary d-flex align-items-center text-truncate pe-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-person-circle me-1 flex-shrink-0" viewBox="0 0 16 16">
                                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                                </svg>
                                                <span class="text-truncate"><?= htmlspecialchars($p['owner_name']) ?></span>
                                            </small>
                                            <small class="text-muted flex-shrink-0" style="font-size: 0.75rem;">
                                                <?= date("d/m/Y", strtotime($p['created_at'])) ?>
                                            </small>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="task.php?project_id=<?= $p['project_id'] ?>" class="btn btn-dark btn-sm flex-grow-1 py-2">
                                                Accéder
                                            </a>

                                            <?php if ($is_owner): ?>
                                                <button class="btn btn-outline-secondary btn-sm editBtn px-3"
                                                    data-id="<?= $p['project_id'] ?>"
                                                    data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                                    data-description="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>"
                                                    data-deadline="<?= htmlspecialchars($p['deadline'] ?? '', ENT_QUOTES) ?>"
                                                    title="Paramètres">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.86" />
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>


    <div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow">

                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Nouveau projet</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Nom du projet</label>
                        <input type="text" name="name" class="form-control mb-3" placeholder="Ex: Refonte Site Web" required>

                        <label class="form-label fw-semibold">Description (Optionnel)</label>
                        <textarea name="description" class="form-control mb-3" rows="3" placeholder="Court résumé de l'objectif..."></textarea>

                        <label class="form-label fw-semibold">Date limite / Échéance (Optionnel)</label>
                        <input type="date" name="deadline" class="form-control">
                    </div>
                    <div class="modal-footer border-top-0 d-flex flex-column flex-sm-row gap-2 w-100">
                        <button type="button" class="btn btn-light text-secondary text-decoration-none w-100 order-2 order-sm-1" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="create_project" class="btn btn-dark w-100 order-1 order-sm-2">Créer le projet</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow">

                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Paramètres du projet</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="project_id" id="edit_project_id">

                        <label class="form-label fw-semibold">Nom du projet</label>
                        <input type="text" name="name" id="edit_name" class="form-control mb-3" required>

                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="edit_description" class="form-control mb-3" rows="3"></textarea>

                        <label class="form-label fw-semibold">Date limite / Échéance (Optionnel)</label>
                        <input type="date" name="deadline" id="edit_deadline" class="form-control">

                        <?php if (!empty($update_error)): ?>
                            <div class="alert alert-danger mt-3 py-2 small"><?= htmlspecialchars($update_error) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-top-0 d-flex flex-column flex-sm-row justify-content-between gap-2 w-100">
                        <button type="submit" name="delete_project" class="btn btn-outline-danger w-100 order-2 order-sm-1"
                            onclick="return confirm('Attention : Cette action est irréversible. Tout le contenu du projet sera supprimé. Continuer ?')">
                            Supprimer le projet
                        </button>
                        <button type="submit" name="update_project" class="btn btn-dark w-100 order-1 order-sm-2">Enregistrer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div>
        <?php include 'footer.php'; ?>
    </div>

    <script>
        document.getElementById('showFormBtn').addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('createProjectModal')).show();
        });

        document.querySelectorAll('.editBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('edit_project_id').value = btn.dataset.id;
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_description').value = btn.dataset.description;
                document.getElementById('edit_deadline').value = btn.dataset.deadline;
                new bootstrap.Modal(document.getElementById('editProjectModal')).show();
            });
        });
    </script>
</body>

</html>