<?php
    if (isset($_POST["Connexion"])) {
        try {
            require("db.php");

            if (!isset($_POST['Connexion']) || $_SERVER['REQUEST_METHOD'] != 'POST')
                header('location:../pages/connexion.php');

            if (empty($_POST['email']) || empty($_POST['password']))
                header('location:../pages/connexion.php');

            $email = $_POST["email"];
            $password = $_POST["password"];

            $reqPrep = "SELECT * FROM user WHERE email = :email";
            $req1 = $conn->prepare($reqPrep);
            $req1->execute(array(':email' => $email));
            $user = $req1->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
    
                    session_start();
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['nom'] = $user['nom'];
                    header("location:../pages/accueil.php");

                } else {
         
                    header('location:../pages/connexion.php?error=mdp');
                }
            } else {
         
                header('location:../pages/connexion.php?error=email');
            }

            $conn = NULL;
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
?>
