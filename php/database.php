<?php
// database.php
// Informations de connexion — adapte si besoin

if ($_SERVER['SERVER_NAME'] === 'localhost') {
    // LOCAL (WAMP)
    $host = 'localhost';
    $dbname   = 'bd_unify';
    $username = 'root';
    $password = '';
} else {
    // PROD (IONOS)
    $host = 'db5018695033.hosting-data.io';
    $dbname = 'dbs14801731';
    $username = 'dbu56411';
    $password = '|0^m6U(4SC9Y5jrz?<af';
}

try {
    // Connexion PDO et stockage dans $db (utilisé par index.php)
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // En développement affiche l'erreur, en production logge-la et affiche un message générique
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

try {
    // Connexion PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>