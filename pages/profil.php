<?php
session_start();
if (!isset($_SESSION['authentifie']) || $_SESSION['authentifie'] !== true) {
    header('Location: connexion.php'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// userid
$user_id = $_SESSION['id_user'];

// Connexion à la base de données
require_once '../traitements/db.php';

// Récupération des informations de l'utilisateur
$stmt = $conn->prepare('SELECT * FROM utilisateurs WHERE id = :id');
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Mise à jour des informations de l'utilisateur
    $updateStmt = $conn->prepare('UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, password = :password WHERE id = :id');
    $updateStmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'password' => $password,
        'id' => $user_id
    ]);

    // Actualisation des données utilisateur
    header('Location: profil.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../media/favicon.ico">
    <link rel="stylesheet" type="text/css" href="../style/profil.css">
    <title>Profil</title>
</head>
<body>
    <div class="container">
        <h1>Modifier votre profil</h1>
        <form method="POST">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>

            <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" id="password" name="password">

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>

