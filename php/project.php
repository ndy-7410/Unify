<?php
session_start();
require 'database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Création d'un nouveau projet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO project (user_id, name, description, created_at, updated_at)
                               VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $name, $description]);
        header("Location: project.php");
        exit();
    } else {
        $error = "Le nom du projet est obligatoire.";
    }
}

// Modification d'un projet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $project_id = $_POST['project_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name !== '' && $project_id !== '') {
        $stmt = $pdo->prepare("UPDATE project
                               SET name = ?, description = ?, updated_at = NOW()
                               WHERE project_id = ? AND user_id = ?");
        $stmt->execute([$name, $description, $project_id, $user_id]);
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

// Récupération des projets
$stmt = $pdo->prepare("SELECT project_id, name, description, created_at, updated_at
                       FROM project WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$project = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Unify | Projet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <section class="py-5 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="text-center mb-4">
                    <h1>Mes projets</h1>
                    <p class="text-muted">Accédez à vos projets</p>
                </div>
                <button class="btn btn-dark my-2" id="showFormBtn">Ajouter un projet</button>
            </div>
        </div>
    </section>

    <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

                <?php foreach ($project as $p): ?>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">

                                <p class="card-text"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="card-text small"><?= htmlspecialchars($p['description']) ?></p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">

                                        <a href="task.php?project_id=<?= $p['project_id'] ?>"
                                            class="btn btn-sm btn-outline-secondary">Voir les tâches</a>

                                        <button class="btn btn-sm btn-outline-secondary editBtn"
                                            data-id="<?= $p['project_id'] ?>"
                                            data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>">
                                            Modifier
                                        </button>

                                    </div>

                                    <!-- ICI modification demandée : -->
                                    <small class="text-body-secondary">
                                        Créé le : <?= date("d/m/Y", strtotime($p['created_at'])) ?>
                                        <!-- | Modifié le : <?= date("d/m/Y", strtotime($p['updated_at'])) ?> -->
                                    </small>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <!-- MODAL CREATION -->
    <div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Créer un projet</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">

                        <label class="form-label">Nom du projet</label>
                        <input type="text" name="name" class="form-control mb-3" required>

                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="create_project" class="btn btn-success">Créer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- MODAL MODIFICATION -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Modifier le projet</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="project_id" id="edit_project_id">

                        <label class="form-label">Nom du projet</label>
                        <input type="text" name="name" id="edit_name" class="form-control mb-3" required>

                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>

                        <?php if (!empty($update_error)): ?>
                            <div class="alert alert-danger mt-3"><?= htmlspecialchars($update_error) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delete_project" class="btn btn-danger"
                            onclick="return confirm('Voulez-vous supprimer ce projet ?')">
                            Supprimer
                        </button>
                        <button type="submit" name="update_project" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        // OUVERTURE MODAL AJOUT
        document.getElementById('showFormBtn').addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('createProjectModal')).show();
        });

        // OUVERTURE MODAL EDITION
        document.querySelectorAll('.editBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('edit_project_id').value = btn.dataset.id;
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_description').value = btn.dataset.description;
                new bootstrap.Modal(document.getElementById('editProjectModal')).show();
            });
        });
    </script>
    <div>
        <?php
        include 'footer.php';
        ?>
    </div>
</body>

</html>