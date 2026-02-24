<?php
require 'database.php';
session_start();

$alert = '';

// 🔐 CLÉ SECRÈTE reCAPTCHA (À REMPLACER)
$recaptchaSecret = "6Le1NF4sAAAAABS0E1fxqX-zZhDng7M1OD7p23c8";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirm = trim($_POST["confirm_password"] ?? '');
    $accepted = isset($_POST["accept_terms"]);
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    if (!$accepted) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Vous devez accepter les conditions d’utilisation.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    elseif (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Veuillez remplir tous les champs.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    elseif ($password !== $confirm) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Les mots de passe ne correspondent pas.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    elseif (empty($recaptchaResponse)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Veuillez valider le captcha.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    else {

        // Vérification reCAPTCHA
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . $recaptchaSecret . "&response=" . $recaptchaResponse
        );

        $captchaSuccess = json_decode($verify);

        if (!$captchaSuccess->success) {
            $alert = '<div class="alert alert-danger alert-dismissible fade show">
                Échec de la vérification du captcha.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        } else {

            // Vérifier si email déjà utilisé
            $stmt = $db->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show">
                    Cet email est déjà utilisé.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {

                // Inscription
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $db->prepare(
                    "INSERT INTO user (name, email, password_hash, created_at)
                     VALUES (?, ?, ?, NOW())"
                );
                $insert->execute([$name, $email, $hash]);

                $_SESSION["user_id"] = $db->lastInsertId();
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
    <title>Unify | S’inscrire</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 📌 SCRIPT reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="text-center mb-4">
                <h1 class="fw-bold">S’inscrire</h1>
                <p class="text-muted">Créez votre compte Unify</p>
            </div>

            <?php if ($alert) echo $alert; ?>

            <div class="card p-4 shadow-sm">
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="accept_terms" required>
                        <label class="form-check-label">
                            J’accepte les <a href="info.php">conditions d’utilisation de Unify</a>
                        </label>
                    </div>

                    <!-- 🧩 CAPTCHA -->
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6Le1NF4sAAAAAEXtCZIjRrfL9S4fylbwXyl8Y_Yr"></div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        S’inscrire
                    </button>
                </form>
            </div>

            <div class="text-center mt-3">
                <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
