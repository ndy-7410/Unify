<!DOCTYPE html>
<html lang="fr">
<?php
require 'database.php';
require 'stat.php';
session_start();
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Un peu de CSS pour peaufiner le design */
        .hero-section {
            background: linear-gradient(180deg, rgba(248, 249, 250, 1) 0%, rgba(255, 255, 255, 1) 100%);
        }

        .changelog-date {
            font-size: 0.85rem;
            min-width: 100px;
            text-align: right;
        }

        /* Sur mobile, on aligne la date à gauche quand elle passe en dessous */
        @media (max-width: 575.98px) {
            .changelog-date {
                text-align: left;
                margin-top: 8px;
            }
        }

        .feature-icon-box {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <div>
        <?php include 'navbar.php'; ?>
    </div>

    <div class="px-3 px-md-4 py-5 my-5 text-center">
        <img class="d-block mx-auto mb-4" src="logo.png" alt="Logo Unify" style="height: 70px; width: auto;" />
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
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-5">
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
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a type="button" class="btn btn-secondary btn-lg px-4 gap-3" href="#changelog">
                        Voir les mises à jour
                    </a>
                </div>
            </div>

            <p class="text-muted mt-3 small px-3">
                <small>⚠️ Les données sur la version de démonstration peuvent être réinitialisées périodiquement.</small>
            </p>
        </div>
    </div>

    <div class="container px-4 py-5" id="features">
        <h2 class="pb-2 border-bottom mb-4">Fonctionnalités Clés</h2>
        <div class="row g-4 py-4 row-cols-1 row-cols-md-2 row-cols-lg-3">

            <div class="col d-flex align-items-start">
                <div class="feature-icon-box bg-primary-subtle text-primary me-3 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-kanban" viewBox="0 0 16 16">
                        <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h11zM6.5 14h1v-3.5a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 0-.5.5V14h1.5V14zM13 14v-2.5a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 0-.5.5V14h3zM8 14v-2.5a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 0-.5.5V14h3zM14 2H2v12h12V2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="fs-4 text-body-emphasis">Gestion de Projet</h3>
                    <p>Créez des projets illimités, organisez vos espaces de travail et gardez une vue d'ensemble sur l'avancement.</p>
                </div>
            </div>

            <div class="col d-flex align-items-start">
                <div class="feature-icon-box bg-success-subtle text-success me-3 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z" />
                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="fs-4 text-body-emphasis">Suivi des Tâches</h3>
                    <p>Ajoutez des tâches, définissez des priorités (Urgent, Moyen...) et mettez à jour les statuts en temps réel.</p>
                </div>
            </div>

            <div class="col d-flex align-items-start">
                <div class="feature-icon-box bg-warning-subtle text-warning me-3 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1h8zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
                    </svg>
                </div>
                <div>
                    <h3 class="fs-4 text-body-emphasis">Collaboration</h3>
                    <p>Invitez des collaborateurs par email, travaillez en équipe sur les mêmes projets et suivez qui fait quoi.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center my-5 px-3">
        <h2 class="pb-2 border-bottom mb-4">Statistiques Unify</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">

            <div class="col">
                <div class="card shadow-sm p-4 h-100">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($userCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted mb-0">Utilisateurs enregistrés</p>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-sm p-4 h-100">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($projectCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted mb-0">Projets créés</p>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-sm p-4 h-100">
                    <h3 class="fw-bold"><?php echo htmlspecialchars($taskCount, ENT_QUOTES); ?></h3>
                    <p class="text-muted mb-0">Tâches enregistrées</p>
                </div>
            </div>

        </div>
    </div>

    <div class="container py-5 px-3 px-md-4" id="changelog">
        <h2 class="pb-3 mb-4 border-bottom">Journal des modifications</h2>

        <div class="list-group shadow-sm">

            <div class="list-group-item d-flex gap-3 py-3 bg-body-tertiary">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold text-primary">v1.4.0 - Responsive & Simulateur de Paiement</h6>
                        <p class="mb-0 opacity-75 mt-2">Adaptation mobile et refonte du système d'abonnement.</p>
                        <ul class="mt-2 mb-1 small text-muted ps-3">
                            <li><strong>Responsive Design :</strong> Optimisation de l'affichage sur téléphones et tablettes (tableaux défilants, menus adaptés).</li>
                            <li><strong>Paiement :</strong> Remplacement de l'API externe par un simulateur interne de carte bancaire pour les soutenances.</li>
                            <li><strong>Abonnements :</strong> Possibilité de rétrograder (downgrade) son forfait vers une offre inférieure.</li>
                            <li><strong>Interface :</strong> Ajout d'une fonctionnalité pour fermer la barre de navigation sur mobile en cliquant à l'extérieur.</li>
                            <li><strong>Maintenance :</strong> Création d'une page de maintenance personnalisée.</li>
                        </ul>
                    </div>
                    <small class="opacity-50 text-nowrap changelog-date">16 Mars 2026</small>
                </div>
            </div>

            <div class="list-group-item d-flex gap-3 py-3">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold">v1.3.0 - Ergonomie & Temps réel</h6>
                        <p class="mb-0 opacity-75 mt-2">Refonte visuelle et sauvegarde automatique.</p>
                        <ul class="mt-2 mb-1 small text-muted ps-3">
                            <li><strong>Auto-save :</strong> Sauvegarde automatique des tâches sans recharger la page.</li>
                            <li><strong>Design :</strong> Nouveaux menus déroulants style "pilule" avec emojis de couleur.</li>
                            <li><strong>Interface :</strong> Affichage des membres sous forme d'avatars personnalisés (Chips).</li>
                            <li><strong>Notifications :</strong> Nouvelles alertes de sauvegarde flottantes (glassmorphism).</li>
                            <li><strong>Navigation :</strong> Refonte complète de la barre de menu et intégration du changement de mot de passe.</li>
                            <li><strong>Authentification :</strong> Pages de connexion et d'inscription modernisées et recentrées.</li>
                        </ul>
                    </div>
                    <small class="opacity-50 text-nowrap changelog-date">26 Fév. 2026</small>
                </div>
            </div>

            <div class="list-group-item d-flex gap-3 py-3">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold">v1.2.0 - Module de Collaboration</h6>
                        <p class="mb-0 opacity-75 mt-2">Mise en place du travail d'équipe.</p>
                        <ul class="mt-2 mb-1 small text-muted ps-3">
                            <li>Système d'invitation par email.</li>
                            <li>Gestion des membres (Ajouter/Exclure/Quitter).</li>
                            <li>Notification des invitations dans la barre de navigation.</li>
                            <li>Affichage du propriétaire du projet.</li>
                        </ul>
                    </div>
                    <small class="opacity-50 text-nowrap changelog-date">25 Fév. 2026</small>
                </div>
            </div>

            <div class="list-group-item d-flex gap-3 py-3">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold">v1.1.0 - Gestion avancée</h6>
                        <p class="mb-0 opacity-75 mt-2">Amélioration du détail des tâches.</p>
                        <ul class="mt-2 mb-1 small text-muted ps-3">
                            <li>Ajout des niveaux de priorité.</li>
                            <li>Ajout des statuts d'avancement.</li>
                            <li>Tri des colonnes.</li>
                        </ul>
                    </div>
                    <small class="opacity-50 text-nowrap changelog-date">15 Jan. 2026</small>
                </div>
            </div>

            <div class="list-group-item d-flex gap-3 py-3">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold">v1.0.0 - Lancement</h6>
                        <p class="mb-0 opacity-75 mt-2">Première version publique.</p>
                        <ul class="mt-2 mb-1 small text-muted ps-3">
                            <li>Inscription et Authentification.</li>
                            <li>CRUD Projets et Tâches.</li>
                        </ul>
                    </div>
                    <small class="opacity-50 text-nowrap changelog-date">05 Jan. 2026</small>
                </div>
            </div>

        </div>
    </div>

    <div>
        <?php include 'footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>