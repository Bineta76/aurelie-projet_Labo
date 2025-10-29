<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();
include 'includes/header.php';

// Configuration de la base de données
$host = 'localhost';
$db = 'labo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mdp    = $_POST['mdp'] ?? '';
    $numero = preg_replace('/\D/', '', $_POST['numero_de_securite_sociale'] ?? '');

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($numero)) {
        $message = "❌ Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Adresse email invalide.";
    } elseif (strlen($numero) !== 15) {
        $message = "❌ Numéro invalide : il doit contenir exactement 15 chiffres.";
    } elseif (!function_exists('bcmod')) {
        $message = "❌ Erreur : l'extension BCMath doit être activée sur le serveur.";
    } else {
        // Calcul de la clé attendue
        $cleAttendue = 97 - bcmod(substr($numero, 0, 13), 97);
        $cle         = intval(substr($numero, -2));

        $clesTest = ['282097645321123']; // Numéros de test autorisés

        if ($cle !== $cleAttendue && !in_array($numero, $clesTest)) {
            $message = "❌ Numéro invalide : clé de contrôle incorrecte (attendue : $cleAttendue).";
        } else {
            // Vérifie si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $message = "❌ Un compte avec cet email existe déjà.";
            } else {
                try {
                    $hash = password_hash($mdp, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        INSERT INTO patient (nom, prenom, email, numero_de_securite_sociale, mot_de_passe) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$nom, $prenom, $email, $numero, $hash]);

                    $message = "✅ Inscription réussie, redirection...";
                    header("Refresh:3; url=index.php");
                    exit;
                } catch (PDOException $e) {
                    $message = "❌ Erreur lors de l'inscription : " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php if (!empty($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="nom" placeholder="Nom" required><br>
        <input type="text" name="prenom" placeholder="Prénom" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="numero_de_securite_sociale" placeholder="Numéro de sécurité sociale" required><br>
        <input type="password" name="mdp" placeholder="Mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
