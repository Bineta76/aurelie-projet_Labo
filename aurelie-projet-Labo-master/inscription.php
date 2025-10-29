<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

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

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $message = "❌ Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Adresse email invalide.";
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
                    INSERT INTO patient (nom, prenom, email, mot_de_passe)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$nom, $prenom, $email, $hash]);

                $message = "✅ Inscription réussie ! Redirection en cours...";
                header("Refresh:3; url=index.php");
                exit;
            } catch (PDOException $e) {
                $message = "❌ Erreur lors de l'inscription : " . $e->getMessage();
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
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="nom" placeholder="Nom" required><br>
        <input type="text" name="prenom" placeholder="Prénom" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="mdp" placeholder="Mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
