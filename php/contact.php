<!DOCTYPE html>
<html lang="fr">
<?php
session_start();
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <div>
        <?php include 'navbar.php'; ?>
    </div>

    <section class="py-5 flex-grow-1 d-flex align-items-center px-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">

                    <div class="text-center mb-5">
                        <h1 class="fw-bold mb-3">Nous contacter</h1>
                        <p class="text-muted">Une question ? Un problème ? Écrivez-nous.</p>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">

                            <form action="https://api.web3forms.com/submit" method="POST">

                                <input type="hidden" name="access_key" value="e0e4b032-c218-4050-b47a-e04027259fc6" />
                                <input type="hidden" name="subject" value="Nouveau message sur le site Unify" />
                                <input type="hidden" name="from_name" value="Unify | Contact" />

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="nom@exemple.com" required>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label fw-semibold">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Votre message..." required></textarea>
                                </div>

                                <div class="d-grid mt-2">
                                    <button type="submit" class="btn btn-dark btn-lg rounded-pill fw-medium">Envoyer le message</button>
                                </div>

                            </form>

                        </div>
                    </div>

                    <div class="text-center mt-4 text-muted small">
                        <p>Nous vous répondrons dans les plus brefs délais.</p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div>
        <?php include 'footer.php'; ?>
    </div>

</body>

</html>