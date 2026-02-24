<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Unify | Informations légales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div>
        <?php
        include 'navbar.php';
        ?>
    </div>

    <div class="container py-5">

        <h1 class="text-center fw-bold mb-4">Informations légales — Unify</h1>
        <p class="text-center text-muted mb-5">CGU, Mentions légales, Confidentialité et Cookies</p>

        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h2 class="fw-semibold">Source</h2>

            <h5>Intelligence Artificiel</h5>
            <p>Le site à été fait à l'aide de l'intelligence artificiel</p>


        </div>

        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h2 class="fw-semibold">1. Conditions Générales d’Utilisation (CGU)</h2>
            <p>Les présentes Conditions Générales d’Utilisation ont pour objet de définir les modalités d’accès et
                d’utilisation du site Unify.
                L’utilisateur accepte sans réserve les présentes conditions.</p>

            <h5>Objet du site</h5>
            <p>Unify est une plateforme collaborative de gestion de projets permettant la création, le suivi et
                l’organisation de tâches.</p>

            <h5>Accès & Compte</h5>
            <p>
                • Accès libre au site.<br>
                • Certaines fonctions nécessitent un compte.<br>
                • L'utilisateur doit fournir des informations exactes et maintenir la confidentialité de ses
                identifiants.
            </p>

            <h5>Utilisation</h5>
            <p>L’utilisateur s’engage à ne pas perturber le fonctionnement du site ou publier du contenu illicite.</p>
        </div>

        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h2 class="fw-semibold">2. Politique de Confidentialité</h2>
            <p>Données collectées : nom, email, projets, adresse IP. Elles sont utilisées pour la gestion de compte, la
                sécurité et l’amélioration du service.</p>

            <h5>Droits utilisateur :</h5>
            <p>
                Accès – Rectification – Suppression – Portabilité – Opposition.<br>
                Possibilité de réclamation auprès de la CNIL.
            </p>
        </div>

        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h2 class="fw-semibold">3. Politique de Cookies</h2>
            <p>Cookies utilisés pour le bon fonctionnement, la mesure d’audience et l’expérience utilisateur.</p>

            <h5>Choix de l’utilisateur :</h5>
            <p>
                Accepter tous les cookies<br>
                Refuser<br>
                Paramétrer individuellement
            </p>
        </div>

        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="fw-semibold">4. Mentions Légales</h2>
            <p>
                Unify – Nom de l’entreprise<br>
                Email : contact@andyl.fr<br>
                <!-- SIRET / RCS — à compléter -->
            </p>
            
            <!-- <h5>Hébergeur :</h5>
            <p>
                Nom : Andy<br>
                Adresse : Haute-Savoie, 74000 Annecy<br>
                Contact : contact@andyl.fr
            </p> -->

            <p class="mt-3">Tous les éléments présents sur le site sont protégés par le droit d’auteur.</p>
        </div>

    </div>
    <div>
        <?php
        include 'footer.php';
        ?>
    </div>
</body>

</html>