<?php
// ----------------------------------------------------
// Activation des erreurs PHP
// ----------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------------------
// Démarrage de la session et inclusion
// ----------------------------------------------------
session_start();
include 'includes/header.php';

// ----------------------------------------------------
// Connexion à la base de données
// ----------------------------------------------------
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}

// ----------------------------------------------------
// Ajout d’un nouveau compte rendu
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['texte'])) {
    $texte = trim($_POST['texte']);
    if ($texte !== '') {
        // Prépare et exécute UNE SEULE requête
        $stmt = $pdo->prepare("INSERT INTO bilan (texte) VALUES (?)");
        $stmt->execute([$texte]);
        header("Location: " . $_SERVER['PHP_SELF']); // redirection vers la même page
        exit;
    }
}

// ----------------------------------------------------
// Suppression d’un compte rendu
// ----------------------------------------------------
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM bilan WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . $_SERVER['PHP_SELF']); // redirection vers la même page
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Rendu Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4 text-center">🩺 Gestion des comptes rendus médicaux</h1>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Ajouter un nouveau compte rendu</div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <textarea name="texte" class="form-control" rows="4" placeholder="Écris ton compte rendu ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Liste des comptes rendus -->
    <?php
    $stmt = $pdo->query("SELECT * FROM bilan ORDER BY id DESC");
    if ($stmt->rowCount() > 0):
        foreach ($stmt as $row):
    ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">📝 Compte rendu #<?= htmlspecialchars($row['id']) ?></h5>
                <p class="text-muted">Créé le <?= date('d/m/Y à H:i', strtotime($row['date_creation'] ?? 'now')) ?></p>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['texte'])) ?></p>
                <a href="?supprimer=<?= urlencode($row['id']) ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Supprimer ce compte rendu ?');">
                   Supprimer
                </a>
            </div>
        </div>
    <?php 
        endforeach;
    else:
    ?>
        <div class="alert alert-info text-center">Aucun compte rendu enregistré pour le moment.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
