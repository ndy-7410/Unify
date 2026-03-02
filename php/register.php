<?php
require 'database.php';
session_start();

$alert = '';

// 🔐 CLÉ SECRÈTE reCAPTCHA
$recaptchaSecret = "6Le1NF4sAAAAABS0E1fxqX-zZhDng7M1OD7p23c8";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirm = trim($_POST["confirm_password"] ?? '');
    $accepted = isset($_POST["accept_terms"]);
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // 1. Vérification des conditions
    if (!$accepted) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                    </svg>
                    Vous devez accepter les conditions d’utilisation.
                  </div>';
    }
    // 2. Vérification des champs vides
    elseif (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Veuillez remplir tous les champs.
                  </div>';
    }
    // 3. Vérification correspondance mot de passe
    elseif ($password !== $confirm) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill me-2" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    </svg>
                    Les mots de passe ne correspondent pas.
                  </div>';
    }
    // 4. Vérification du Captcha
    elseif (empty($recaptchaResponse)) {
        $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-robot me-2" viewBox="0 0 16 16">
                        <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                        <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                    </svg>
                    Veuillez valider le captcha.
                  </div>';
    } else {

        // Vérification API Google
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
                . $recaptchaSecret . "&response=" . $recaptchaResponse
        );

        $captchaSuccess = json_decode($verify);

        if (!$captchaSuccess->success) {
            $alert = '<div class="alert alert-danger" role="alert">Échec de la vérification du captcha.</div>';
        } else {

            // Vérifier si email déjà utilisé (Correction: $pdo au lieu de $db)
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $alert = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill me-2" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            Cet email est déjà utilisé.
                          </div>';
            } else {

                // Inscription
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare(
                    "INSERT INTO user (name, email, password_hash, created_at)
                     VALUES (?, ?, ?, NOW())"
                );
                $insert->execute([$name, $email, $hash]);

                $_SESSION["user_id"] = $pdo->lastInsertId();
                $_SESSION["name"] = $name;

                header("Location: project.php");
                exit();
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
    <title>Unify | S’inscrire</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .login-card {
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 1rem;
            transition: box-shadow 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .05) !important;
        }

        .login-container {
            min-height: 80vh;
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
                    <h1 class="h3 fw-bold">Créer un compte</h1>
                    <p class="text-muted">Rejoignez Unify pour gérer vos projets.</p>
                </div>

                <?php if ($alert) echo $alert; ?>

                <div class="card login-card shadow-sm p-4">
                    <div class="card-body">
                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nom complet</label>
                                <input type="text" name="name" class="form-control" placeholder="Jean Dupont" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Adresse email</label>
                                <input type="email" name="email" class="form-control" placeholder="nom@exemple.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mot de passe</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" required>
                                <label class="form-check-label small text-muted" for="accept_terms">
                                    J’accepte les <a href="info.php" class="text-dark text-decoration-underline">conditions d’utilisation</a> de Unify
                                </label>
                            </div>

                            <div class="mb-4 d-flex justify-content-center">
                                <div class="g-recaptcha" data-sitekey="6Le1NF4sAAAAAEXtCZIjRrfL9S4fylbwXyl8Y_Yr"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-lg">
                                    S’inscrire
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">Déjà un compte ? <a href="login.php" class="text-dark fw-semibold text-decoration-none">Se connecter</a></p>
                </div>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>