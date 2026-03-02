<?php
require 'database.php';
session_start();

$alert = '';

// 🔐 CLÉ SECRÈTE reCAPTCHA
$recaptchaSecret = "6Le1NF4sAAAAABS0E1fxqX-zZhDng7M1OD7p23c8";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Vérification champs
    if (empty($email) || empty($password)) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Veuillez remplir tous les champs.
                  </div>';
    }
    // Vérification captcha
    elseif (empty($recaptchaResponse)) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Veuillez valider le captcha.
                  </div>';
    } else {

        // Vérifier captcha auprès de Google
        // Note: Assurez-vous que allow_url_fopen est activé sur votre serveur pour file_get_contents
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . $recaptchaSecret . "&response=" . $recaptchaResponse
        );

        $captchaSuccess = json_decode($verify);

        if (!$captchaSuccess->success) {
            $alert = '<div class="alert alert-danger" role="alert">Échec de la vérification du captcha.</div>';
        } else {

            // Recherche utilisateur (Correction: $pdo au lieu de $db)
            $stmt = $pdo->prepare("SELECT user_id, name, email, password_hash FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                header("Location: project.php");
                exit();
            } else {
                $alert = '<div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>
                            Email ou mot de passe incorrect.
                          </div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unify | Se connecter</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>
        .login-card {
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 1rem;
            transition: box-shadow 0.3s ease;
        }
        .login-card:hover {
            box-shadow: 0 1rem 3rem rgba(0,0,0,.05)!important;
        }
        /* Centrage vertical du contenu */
        .login-container {
            min-height: 80vh; /* Prend au moins 80% de la hauteur de vue */
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body class="bg-white d-flex flex-column min-vh-100">

<?php include 'navbar.php'; ?>

<div class="container login-container flex-grow-1">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5 col-xl-4">

            <div class="text-center mb-4">
                <img src="logo.png" alt="Unify Logo" height="40" class="mb-3">
                <h1 class="h3 fw-bold">Bon retour !</h1>
                <p class="text-muted">Connectez-vous pour accéder à vos projets.</p>
            </div>

            <?php if ($alert) echo $alert; ?>

            <div class="card login-card shadow-sm p-4">
                <div class="card-body">
                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse email</label>
                            <input type="email" name="email" class="form-control" placeholder="nom@exemple.com" required>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-semibold">Mot de passe</label>
                                </div>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <div class="g-recaptcha" data-sitekey="6Le1NF4sAAAAAEXtCZIjRrfL9S4fylbwXyl8Y_Yr"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">Pas encore de compte ? <a href="register.php" class="text-dark fw-semibold text-decoration-none">Créer un compte</a></p>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>