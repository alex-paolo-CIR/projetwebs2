<?php

	// Fonctions pour valider les champs
	function nettoyer_donnees($donnees){
		$donnees = trim($donnees);
		$donnees = stripslashes($donnees);
		$donnees = htmlspecialchars($donnees);
		return $donnees;
	}
	
	function valider_NomPrenom($NomPrenom){
		return preg_match("/^[a-zA-Z\s'-]{1,40}$/",$NomPrenom);
	}
	

	if(isset($_POST["Ajouter"])){
		try{
			require("db.php");               
		
			if (!isset($_POST['Ajouter']) || $_SERVER['REQUEST_METHOD'] != 'POST')
				header('location:../pages/inscription.php');

			if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password_confirm']))
				header('location:../pages/inscription.php');

            if ($_POST['password'] != $_POST['password_confirm'])
                header('location:../pages/inscription.php?error=mdp_confirm');

			// email déjà utilisé
			$reqPrep = "SELECT * FROM utilisateurs WHERE email = :email";
			$req1 = $conn->prepare($reqPrep);
			$req1->execute(array(':email' => $_POST["email"]));
			$user = $req1->fetch(PDO::FETCH_ASSOC);

			if ($user) {
				header('location:../pages/inscription.php?error=email_utilise');
			}
		


			else {
				$nom = nettoyer_donnees($_POST["nom"]);
				$prenom = nettoyer_donnees($_POST["prenom"]);
				$email = nettoyer_donnees($_POST["email"]);
				$password = password_hash(nettoyer_donnees($_POST["password"]), PASSWORD_BCRYPT);

				if (valider_NomPrenom($nom) == 0 || valider_NomPrenom($prenom) == 0) {
					header('location:../pages/inscription.php?error=nom_prenom_invalide');
					exit;
				}

				$reqPrep= "INSERT INTO utilisateurs VALUES (NULL, :nom, :prenom, :email, :password, TRUE, FALSE, CURRENT_TIMESTAMP)";

				$req1 = $conn->prepare($reqPrep);

				$req1->execute(array(':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':password' => $password));

				$conn= NULL;
				header("location:../pages/connexion.php?success=inscription");
			}
		}                 
		catch(Exception $e){
			die("Erreur : " . $e->getMessage());
        }
	}

?>