<!DOCTYPE html>
<html lang="fr">
<?php

require 'database.php';
require 'stat.php';
session_start();
?>

<head>
    <meta charset="UTF-8" />
    <title>Unify | Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div>
        <?php
        include 'navbar.php';
        ?>
    </div>
    <div class="px-4 py-5 my-5 text-center">
        <img class="d-block mx-auto mb-4" src="logo.png" alt="" height="50" />
        <h1 class="display-5 fw-bold text-body-emphasis">Unify</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">
                Bonjour, je m'appelle Andy.
                Dans le cadre de mon projet de fin d’année au lycée,
                j’ai choisi de développer un site web de gestion de projet nommé Unify.
                Ce site est encore en phase de démarrage et en cours de développement.
                Il est conçu avec les langages HTML, CSS, JavaScript, PHP et SQL.
                L’objectif principal d’Unify est d’offrir une plateforme sécurisée et
                facile à utiliser pour organiser et suivre l’avancement de projets.
            </p>

            <?php if (isset($_SESSION["user_id"])) { ?>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a type="button" class="btn btn-dark btn-lg px-4 gap-3" href="project.php">
                        Voir mes projets
                    </a>
                </div>
            <?php } else { ?>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a type="button" class="btn btn-dark btn-lg px-4 gap-3" href="register.php">
                        Commencer
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="container px-4 py-5" id="hanging-icons">
        <h2 class="pb-2 border-bottom">Les fonctions</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
            <div class="col d-flex align-items-start">
                <div
                    class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg class="bi" width="1em" height="1em" aria-hidden="true">
                        <use xlink:href="#toggles2"></use>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Gestion de projet</h3>
                    <p>
                        Grâce à Unify, vous pouvez facilement gérer vos projets en ajoutant des tâches.
                    </p>
                </div>
            </div>
            <div class="col d-flex align-items-start">
                <div
                    class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg class="bi" width="1em" height="1em" aria-hidden="true">
                        <use xlink:href="#cpu-fill"></use>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Ajout de projet</h3>
                    <p>
                        Grâce à Unify, vous pouvez ajouter une quantité illimitée de projets.
                    </p>
                </div>
            </div>
            <div class="col d-flex align-items-start">
                <div
                    class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg class="bi" width="1em" height="1em" aria-hidden="true">
                        <use xlink:href="#cpu-fill"></use>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Ajout de tâche</h3>
                    <p>
                        Grâce à Unify, vous pouvez facilement ajouter une nouvelle tâche et déterminer son état et sa priorité.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center my-5">
        <h2 class="pb-3 border-bottom">Statistiques Unify</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">

            <div class="col">
                <div class="card shadow-sm p-4">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($userCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted">Utilisateurs enregistrés</p>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-sm p-4">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($projectCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted">Projets créés</p>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-sm p-4">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($taskCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted">Tâches enregistrées</p>
                </div>
            </div>

        </div>
    </div>
    <div>
        <?php
        include 'footer.php';
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>