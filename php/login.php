<?php
require 'database.php';
session_start();

$alert = '';

// 🔐 CLÉ SECRÈTE reCAPTCHA (À REMPLACER)
$recaptchaSecret = "6Le1NF4sAAAAABS0E1fxqX-zZhDng7M1OD7p23c8";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Vérification champs
    if (empty($email) || empty($password)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Veuillez remplir tous les champs.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }

    // Vérification captcha
    elseif (empty($recaptchaResponse)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show">
            Veuillez valider le captcha.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    } else {

        // Vérifier captcha auprès de Google
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

            // Recherche utilisateur
            $stmt = $db->prepare("SELECT user_id, name, email, password_hash FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                header("Location: project.php");
                exit();
            } else {
                $alert = '<div class="alert alert-warning alert-dismissible fade show">
                    Email ou mot de passe incorrect.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
    <title>Unify | Se connecter</title>

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
                <h1>Se connecter</h1>
                <p class="text-muted">Accédez à votre compte Unify</p>
            </div>

            <?php if ($alert) echo $alert; ?>

            <div class="card p-4 shadow-sm">
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <!-- 🧩 CAPTCHA -->
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6Le1NF4sAAAAAEXtCZIjRrfL9S4fylbwXyl8Y_Yr"></div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        Se connecter
                    </button>
                </form>
            </div>

            <div class="text-center mt-3">
                <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
