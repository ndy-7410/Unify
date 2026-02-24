<!doctype html>
<html lang="en" data-bs-theme="auto">

<?php

require 'database.php';
require 'stat.php';
session_start();
?>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="" />
  <meta
    name="author"
    content="Mark Otto, Jacob Thornton, and Bootstrap contributors" />
  <meta name="generator" content="Astro v5.13.2" />
  <title>Unify | Prix</title>
  <link
    rel="canonical" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div>
    <?php
    include 'navbar.php';
    ?>
  </div>
  <div class="container py-3">
    <header>
      <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
        <h1 class="display-4 fw-normal text-body-emphasis">Les prix</h1>
        <p class="fs-5 text-body-secondary">
          Description
        </p>
      </div>
    </header>
    <main>
      <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        <div class="col">
          <div class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
              <h4 class="my-0 fw-normal">Starter</h4>
            </div>
            <div class="card-body">
              <h1 class="card-title pricing-card-title">
                0€ <small class="text-body-secondary fw-light">/ mois</small>
              </h1>
              <ul class="list-unstyled mt-3 mb-4">
                <li>Jusqu’à 3 projets</li>
                <li>Jusqu’à 30 tâche</li>
                <li><br></li>
                <li><br></li>
              </ul>
              <div>
                <?php if (isset($_SESSION["user_id"])) { ?>
                  <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a type="button" class="w-100 btn btn-lg btn-primary" href="project.php">
                      Vous posseder déjà cette abonnement
                    </a>
                  </div>
                <?php } else { ?>
                  <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a type="button" class="w-100 btn btn-lg btn-primary" href="register.php">
                      Commencer
                    </a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card mb-4 rounded-3 shadow-sm border-primary">
            <div class="card-header py-3 text-bg-primary border-primary">
              <h4 class="my-0 fw-normal">Pro</h4>
            </div>
            <div class="card-body">
              <h1 class="card-title pricing-card-title">
                5€ <small class="text-body-secondary fw-light">/ mois</small>
              </h1>
              <ul class="list-unstyled mt-3 mb-4">
                <li>Jusqu’à 5 projets</li>
                <li>Jusqu’à 50 tâche</li>
                <li>Collaboration jusqu’à 10 utilisateurs</li>
                <li><br></li>
              </ul>
              <button type="button" class="w-100 btn btn-lg btn-primary">
                Commencer
              </button>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
              <h4 class="my-0 fw-normal">Business</h4>
            </div>
            <div class="card-body">
              <h1 class="card-title pricing-card-title">
                15€ <small class="text-body-secondary fw-light">/ mois</small>
              </h1>
              <ul class="list-unstyled mt-3 mb-4">
                <li>Projets illimité</li>
                <li>Tâche illimité</li>
                <li>Collaboration illimité</li>
                <li>Support prioritaire</li>
              </ul>
              <button type="button" class="w-100 btn btn-lg btn-primary">
                Commencer
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <div>
    <?php
    include 'footer.php';
    ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>