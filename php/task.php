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

// Récup projet
$sqlProject = "SELECT name, description FROM project WHERE project_id = :project_id AND user_id = :user_id";
$stmtProject = $pdo->prepare($sqlProject);
$stmtProject->execute(['project_id' => $project_id, 'user_id' => $user_id]);
$project = $stmtProject->fetch(PDO::FETCH_ASSOC);
if (!$project)
    die("Projet introuvable !");

// tri
$sort_column = 't.created_at';
$sort_order = 'ASC';
$valid_columns = ['status', 'priority'];

if (isset($_GET['sort']) && in_array($_GET['sort'], $valid_columns))
    $sort_column = $_GET['sort'] == 'status' ? 's.status_name' : 'p.priority_name';
if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']))
    $sort_order = strtoupper($_GET['order']);

// POST CRUD
$task_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create_task'])) {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status_id = intval($_POST['status_id'] ?? 1);
        $priority_id = intval($_POST['priority_id'] ?? 1);

        if ($name != '') {
            $stmt = $pdo->prepare("INSERT INTO task(project_id,user_id,name,description,status_id,priority_id,created_at,updated_at)VALUES(?,?,?,?,?,?,NOW(),NOW())");
            $stmt->execute([$project_id, $user_id, $name, $description, $status_id, $priority_id]);
            header("Location: task.php?project_id=$project_id");
            exit();
        } else
            $task_error = "Le nom de la tâche est obligatoire.";
    }

    if (isset($_POST['update_task'])) {
        $task_id = intval($_POST['task_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $status_id = intval($_POST['status_id']);
        $priority_id = intval($_POST['priority_id']);

        if ($name != '') {
            $stmt = $pdo->prepare("UPDATE task SET name=?,description=?,status_id=?,priority_id=?,updated_at=NOW() WHERE task_id=? AND user_id=?");
            $stmt->execute([$name, $description, $status_id, $priority_id, $task_id, $user_id]);
            header("Location: task.php?project_id=$project_id");
            exit();
        } else
            $task_error = "Le nom de la tâche est obligatoire.";
    }

    if (isset($_POST['delete_task'])) {
        $task_id = intval($_POST['task_id']);
        $stmt = $pdo->prepare("DELETE FROM task WHERE task_id=? AND user_id=?");
        $stmt->execute([$task_id, $user_id]);
        header("Location: task.php?project_id=$project_id");
        exit();
    }
}

// recup tâches
$sqlTasks = "SELECT t.task_id,t.name,t.description,s.status_name AS status,p.priority_name AS priority
          FROM task t
          JOIN status s ON t.status_id=s.status_id
          JOIN priority p ON t.priority_id=p.priority_id
          WHERE t.project_id=:project_id
          ORDER BY $sort_column $sort_order";
$stmtTasks = $pdo->prepare($sqlTasks);
$stmtTasks->execute(['project_id' => $project_id]);
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Unify | Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- HEADER style project.php -->
    <section class="py-5 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="text-center mb-4">
                    <h1><?= htmlspecialchars($project['name']) ?></h1>
                    <p class="text-muted"><?= htmlspecialchars($project['description']) ?></p>
                </div>
                <!-- bouton modal -->
                <button class="btn btn-dark" id="showCreateTask">Ajouter une tâche</button>
              	<button class="btn btn-dark">Ajouter un collaborateur</button>
            </div>
        </div>
    </section>

    <!-- SECTION style album -->
    <div class="album py-5 bg-body-tertiary">
        <div class="container">

            <!-- Tri -->
            <div class="text-center mb-4">
                <span class="fw-semibold">Trier par :</span>
                <a href="?project_id=<?= $project_id ?>&sort=status&order=DESC" class="btn btn-secondary btn-sm">Statut
                    ↑</a> |
                <a href="?project_id=<?= $project_id ?>&sort=status&order=ASC" class="btn btn-secondary btn-sm">Statut
                    ↓</a> |
                <a href="?project_id=<?= $project_id ?>&sort=priority&order=DESC"
                    class="btn btn-secondary btn-sm">Priorité ↑</a> |
                <a href="?project_id=<?= $project_id ?>&sort=priority&order=ASC"
                    class="btn btn-secondary btn-sm">Priorité ↓</a>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-hover shadow-sm bg-white">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if ($tasks):
                            foreach ($tasks as $task): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">

                                        <td><input class="form-control" name="name"
                                                value="<?= htmlspecialchars($task['name']) ?>" required></td>
                                        <td><input class="form-control" name="description"
                                                value="<?= htmlspecialchars($task['description']) ?>"></td>

                                        <td>
                                            <select name="status_id" class="form-select">
                                                <option value="1" <?= $task['status'] == 'to do' ? 'selected' : '' ?>>À faire
                                                </option>
                                                <option value="2" <?= $task['status'] == 'in progress' ? 'selected' : '' ?>>En
                                                    cours
                                                </option>
                                                <option value="3" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Terminé
                                                </option>
                                                <option value="4" <?= $task['status'] == 'testing' ? 'selected' : '' ?>>En test
                                                </option>
                                                <option value="5" <?= $task['status'] == 'blocked' ? 'selected' : '' ?>>Bloqué
                                                </option>
                                            </select>
                                        </td>

                                        <td>
                                            <select name="priority_id" class="form-select">
                                                <option value="1" <?= $task['priority'] == 'minimum' ? 'selected' : '' ?>>Minimum
                                                </option>
                                                <option value="2" <?= $task['priority'] == 'medium' ? 'selected' : '' ?>>Moyen
                                                </option>
                                                <option value="3" <?= $task['priority'] == 'important' ? 'selected' : '' ?>>
                                                    Important
                                                </option>
                                                <option value="4" <?= $task['priority'] == 'urgent' ? 'selected' : '' ?>>Urgent
                                                </option>
                                            </select>
                                        </td>

                                        <td>
                                            <button type="submit" name="update_task"
                                                class="btn btn-sm btn-outline-secondary">Modifier</button>
                                            <button type="submit" name="delete_task" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Supprimer cette tâche ?')">Supprimer</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Aucune tâche disponible.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL AJOUT TÂCHE -->
    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle tâche</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">

                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control mb-3" required>

                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control mb-3"></textarea>

                        <label class="form-label">Statut</label>
                        <select name="status_id" class="form-select mb-3">
                            <option value="1">À faire</option>
                            <option value="2">En cours</option>
                            <option value="3">Terminé</option>
                            <option value="4">En test</option>
                            <option value="5">Bloqué</option>
                        </select>

                        <label class="form-label">Priorité</label>
                        <select name="priority_id" class="form-select mb-3">
                            <option value="1">Minimum</option>
                            <option value="2">Moyen</option>
                            <option value="3">Important</option>
                            <option value="4">Urgent</option>
                        </select>

                        <?php if ($task_error): ?>
                            <div class="alert alert-danger"><?= $task_error ?></div>
                        <?php endif; ?>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="create_task" class="btn btn-success">Créer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.getElementById('showCreateTask').onclick = () => new bootstrap.Modal('#createTaskModal').show();
    </script>

</body>

</html>