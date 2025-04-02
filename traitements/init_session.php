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
?>