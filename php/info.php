<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Informations légales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Menu latéral collant pour la navigation */
        .sticky-sidebar {
            position: -webkit-sticky;
            position: sticky;
            top: 2rem;
        }
        
        .nav-link {
            color: var(--bs-secondary);
        }
        
        .nav-link.active {
            color: var(--bs-dark);
            font-weight: 600;
            border-left: 2px solid var(--bs-dark);
        }

        .section-card {
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
        
        .section-card:hover {
            border-color: rgba(0,0,0,.15);
        }
    </style>
</head>

<body class="bg-white" data-bs-spy="scroll" data-bs-target="#legal-nav" data-bs-offset="100">

    <div>
        <?php include 'navbar.php'; ?>
    </div>

    <div class="container py-5 text-center">
        <h1 class="display-5 fw-bold mb-3">Informations Légales</h1>
        <p class="text-muted lead mx-auto" style="max-width: 600px;">
            Transparence et confiance. Retrouvez ici toutes les informations concernant l'utilisation d'Unify.
        </p>
    </div>

    <div class="container pb-5">
        <div class="row g-5">
            
            <div class="col-md-3 d-none d-md-block">
                <nav id="legal-nav" class="nav flex-column sticky-sidebar gap-2">
                    <a class="nav-link active ps-3" href="#source">Source & Crédits</a>
                    <a class="nav-link ps-3" href="#cgu">1. Conditions Générales (CGU)</a>
                    <a class="nav-link ps-3" href="#confidentialite">2. Confidentialité</a>
                    <a class="nav-link ps-3" href="#cookies">3. Cookies</a>
                    <a class="nav-link ps-3" href="#mentions">4. Mentions Légales</a>
                </nav>
            </div>

            <div class="col-md-9">
                
                <div id="source" class="card section-card shadow-sm mb-5 p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-light text-dark rounded-3 p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-code-slash" viewBox="0 0 16 16">
                                <path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294l4-13zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0z"/>
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-0">Source du projet</h2>
                    </div>
                    <div>
                        <h5 class="fw-semibold">Intelligence Artificielle</h5>
                        <p class="text-secondary mb-0">Ce site a été conçu et développé avec l'assistance d'outils d'intelligence artificielle dans le cadre d'un projet pédagogique.</p>
                    </div>
                </div>

                <div id="cgu" class="card section-card shadow-sm mb-5 p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-light text-dark rounded-3 p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5z"/>
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-0">1. Conditions Générales d’Utilisation (CGU)</h2>
                    </div>
                    
                    <p class="text-secondary">Les présentes Conditions Générales d’Utilisation ont pour objet de définir les modalités d’accès et d’utilisation du site Unify. L’utilisateur accepte sans réserve les présentes conditions.</p>

                    <h5 class="fw-semibold mt-4">Objet du site</h5>
                    <p class="text-secondary">Unify est une plateforme collaborative de gestion de projets permettant la création, le suivi et l’organisation de tâches.</p>

                    <h5 class="fw-semibold mt-4">Accès & Compte</h5>
                    <ul class="text-secondary ps-3">
                        <li>L'accès au site est libre.</li>
                        <li>Certaines fonctionnalités nécessitent la création d'un compte utilisateur.</li>
                        <li>L'utilisateur s'engage à fournir des informations exactes et à maintenir la confidentialité de ses identifiants de connexion.</li>
                    </ul>

                    <h5 class="fw-semibold mt-4">Utilisation</h5>
                    <p class="text-secondary mb-0">L’utilisateur s’engage à ne pas perturber le fonctionnement du site, à ne pas tenter d'accès frauduleux et à ne pas publier de contenu illicite.</p>
                </div>

                <div id="confidentialite" class="card section-card shadow-sm mb-5 p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-light text-dark rounded-3 p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-0">2. Politique de Confidentialité</h2>
                    </div>
                    
                    <p class="text-secondary">Nous collectons certaines données (nom, email, projets, adresse IP) uniquement pour la gestion de votre compte, la sécurité de la plateforme et l’amélioration de nos services.</p>

                    <h5 class="fw-semibold mt-4">Vos Droits</h5>
                    <p class="text-secondary mb-0">
                        Conformément à la réglementation (RGPD), vous disposez des droits suivants : accès, rectification, suppression, portabilité et opposition. 
                        Vous avez également la possibilité d'introduire une réclamation auprès de la CNIL.
                    </p>
                </div>

                <div id="cookies" class="card section-card shadow-sm mb-5 p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-light text-dark rounded-3 p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cookie" viewBox="0 0 16 16">
                                <path d="M6 11.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm2-3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 1a6 6 0 1 1 0 12A6 6 0 0 1 8 2z"/>
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-0">3. Politique de Cookies</h2>
                    </div>
                    
                    <p class="text-secondary">Des cookies sont utilisés pour assurer le bon fonctionnement du site (session), mesurer l'audience et améliorer l’expérience utilisateur.</p>

                    <h5 class="fw-semibold mt-4">Gestion des préférences</h5>
                    <p class="text-secondary mb-0">
                        Vous pouvez à tout moment configurer votre navigateur pour accepter, refuser ou paramétrer les cookies individuellement.
                    </p>
                </div>

                <div id="mentions" class="card section-card shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon bg-light text-dark rounded-3 p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022z"/>
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-0">4. Mentions Légales</h2>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-semibold">Éditeur du site</h5>
                            <ul class="list-unstyled text-secondary">
                                <li><strong>Nom :</strong> Unify</li>
                                <li><strong>Responsable :</strong> Andy</li>
                                <li><strong>Contact :</strong> contact@andyl.fr</li>
                            </ul>
                        </div>
                        </div>

                    <div class="alert alert-light mt-3 mb-0 text-center text-muted small border">
                        Tous les éléments présents sur le site (textes, logos, design) sont protégés par le droit d’auteur.
                    </div>
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