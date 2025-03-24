<?php
    if (isset($_POST["Connexion"])) {
        try {
            require("connexion.php");

            // Vérifier que la méthode est POST
            if (!isset($_POST['Connexion']) || $_SERVER['REQUEST_METHOD'] != 'POST')
                header('location:../html/connexion.php');

            // Vérifier que les champs ne sont pas vides
            if (empty($_POST['email']) || empty($_POST['password']))
                header('location:../html/connexion.php');

            $email = $_POST["email"];
            $password = $_POST["password"];

            // Préparer la requête pour chercher l'utilisateur par email
            $reqPrep = "SELECT * FROM user WHERE email = :email";
            $req1 = $conn->prepare($reqPrep);
            $req1->execute(array(':email' => $email));
            $user = $req1->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérification du mot de passe

                if (password_verify($password, $user['password'])) {
                    // Connexion réussie
                    session_start();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['nom'] = $user['nom'];
                    header("location:../html/accueil.html");
                    exit();
                } else {
                    // Mot de passe incorrect
                    header('location:../html/connexion.php?error=mdp');
                }
            } else {
                // Aucun utilisateur trouvé avec cet email
                header('location:../html/connexion.php?error=email');
            }

            $conn = NULL;
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
?>
