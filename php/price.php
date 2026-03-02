<!doctype html>
<html lang="fr" data-bs-theme="auto">

<?php
require 'database.php';
require 'stat.php';
session_start();
?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Unify | Tarifs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pricing-header {
            max-width: 700px;
        }

        /* Effet de survol sur les cartes */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        /* CORRECTION : Taille des icônes unifiée */
        .bi {
            width: 1.2em;  /* Taille adaptée au texte */
            height: 1.2em;
            vertical-align: -0.125em; /* Alignement vertical propre */
            fill: currentColor; /* Prend la couleur du texte parent ou de la classe text-... */
        }
    </style>
</head>

<body>
    <div>
        <?php include 'navbar.php'; ?>
    </div>

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check" viewBox="0 0 16 16">
            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
        </symbol>
        <symbol id="x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </symbol>
    </svg>

    <div class="container py-3">
        <header>
            <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                <h1 class="fw-bold mb-3">Nos Offres</h1>
                <p class="fs-5 text-muted">
                    Commencez petit et grandissez avec nous. Choisissez l'offre parfaitement adaptée à la taille de votre équipe.
                </p>
            </div>
        </header>

        <main>
            <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm h-100">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Starter</h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title">0€<small class="text-body-secondary fw-light">/mois</small></h1>
                            <ul class="list-unstyled mt-3 mb-4 text-start">
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Jusqu'à 3 projets
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Jusqu'à 30 tâches
                                </li>
                                <li class="d-flex mb-2 align-items-center text-muted">
                                    <svg class="bi me-2"><use xlink:href="#x"/></svg> 
                                    Pas de collaboration
                                </li>
                                <li class="d-flex mb-2 align-items-center text-muted">
                                    <svg class="bi me-2"><use xlink:href="#x"/></svg> 
                                    Support standard
                                </li>
                            </ul>
                            <div class="mt-auto">
                                <?php if (isset($_SESSION["user_id"])) { ?>
                                    <button type="button" class="w-100 btn btn-lg btn-outline-primary" disabled>Votre offre actuelle</button>
                                <?php } else { ?>
                                    <a href="register.php" class="w-100 btn btn-lg btn-outline-primary">S'inscrire gratuitement</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card mb-4 rounded-3 shadow border-primary h-100">
                        <div class="card-header py-3 text-bg-primary border-primary">
                            <h4 class="my-0 fw-normal">Pro</h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title">5€<small class="text-body-secondary fw-light">/mois</small></h1>
                            <ul class="list-unstyled mt-3 mb-4 text-start">
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    <strong>5 projets</strong> actifs
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    <strong>50 tâches</strong> par projet
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Collaboration (10 utilisateurs)
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Support email prioritaire
                                </li>
                            </ul>
                            <div class="mt-auto">
                                <button type="button" class="w-100 btn btn-lg btn-primary">Passer en Pro</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm h-100">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Business</h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title">15€<small class="text-body-secondary fw-light">/mois</small></h1>
                            <ul class="list-unstyled mt-3 mb-4 text-start">
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Projets <strong>illimités</strong>
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Tâches <strong>illimitées</strong>
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Collaboration <strong>illimitée</strong>
                                </li>
                                <li class="d-flex mb-2 align-items-center">
                                    <svg class="bi me-2 text-primary"><use xlink:href="#check"/></svg> 
                                    Support dédié 24/7
                                </li>
                            </ul>
                            <div class="mt-auto">
                                <button type="button" class="w-100 btn btn-lg btn-primary">Nous contacter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="display-6 text-center mb-4">Comparatif détaillé</h2>

            <div class="table-responsive">
                <table class="table text-center table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 34%;"></th>
                            <th style="width: 22%;">Starter</th>
                            <th style="width: 22%;">Pro</th>
                            <th style="width: 22%;">Business</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="text-start">Accès public</th>
                            <td><svg class="bi text-primary"><use xlink:href="#check"/></svg></td>
                            <td><svg class="bi text-primary"><use xlink:href="#check"/></svg></td>
                            <td><svg class="bi text-primary"><use xlink:href="#check"/></svg></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Nombre de Projets</th>
                            <td>3</td>
                            <td>5</td>
                            <td>Illimité</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Tâches par projet</th>
                            <td>30</td>
                            <td>50</td>
                            <td>Illimité</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Membres d'équipe</th>
                            <td>-</td>
                            <td>10</td>
                            <td>Illimité</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Statistiques avancées</th>
                            <td>-</td>
                            <td><svg class="bi text-primary"><use xlink:href="#check"/></svg></td>
                            <td><svg class="bi text-primary"><use xlink:href="#check"/></svg></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Support</th>
                            <td>Communauté</td>
                            <td>Email</td>
                            <td>Dédié</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div>
        <?php include 'footer.php'; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>