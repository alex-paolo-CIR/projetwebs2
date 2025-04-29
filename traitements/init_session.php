<?php
session_start();
    require("../traitements/traitement_connexion.php");
    // verif de la session
    if(isset($_SESSION["authentifie"]) and $_SESSION["authentifie"]== TRUE){
        $id_user = $_SESSION['id_user'];
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];
        $email = $_SESSION['email'];
        $authentifie = TRUE;
        $admin = $_SESSION['admin'];
        $user_date_creation = $_SESSION['date_creation'];

    }

    setcookie('user_id', $user['id'], [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => true, // Utilisez HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    if (!isset($_SESSION['authentifie']) && isset($_COOKIE['user_id'])) {
        require("db.php");
    
        $user_id = $_COOKIE['user_id'];
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['authentifie'] = true;
            $_SESSION['admin'] = $user['admin'];
        }
    }


?>