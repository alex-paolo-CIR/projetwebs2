<?php


	// Fonctions pour valider les champs
	function nettoyer_donnees($donnees){
		$donnees = trim($donnees);
		$donnees = stripslashes($donnees);
		$donnees = htmlspecialchars($donnees);
		return $donnees;
	}
	


    if (isset($_POST["Connexion"])) {
        try {
            require("db.php");

            if (!isset($_POST['Connexion']) || $_SERVER['REQUEST_METHOD'] != 'POST')
                header('location:../pages//connexion.php');

            if (empty($_POST['email']) || empty($_POST['password']))
                header('location:../pages/connexion.php?error=empty');

            $email = nettoyer_donnees($_POST["email"]);
            $password = nettoyer_donnees($_POST["password"]);

            $reqPrep = "SELECT * FROM utilisateurs WHERE email = :email";
            $req1 = $conn->prepare($reqPrep);
            $req1->execute(array(':email' => $email));
            $user = $req1->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
    
                    session_start();
                    $_SESSION['id_user'] = $user['id'];
                    $_SESSION['nom'] = $user['nom'];
                    $_SESSION['prenom'] = $user['prenom'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['authentifie'] = TRUE;
                    $_SESSION['admin'] = $user['admin'];
                    $_SESSION['date_creation'] = $user['date_creation'];

                    if (isset($_POST['remember-me'])) {
                        setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), "/"); // Cookie valide 30 jours
                    }

                    
                    if($user['admin'] == 1) {
                        header("location:../pages/admin.php");
                    } else {
                        header("location:../pages/accueil.php");
                    }

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
