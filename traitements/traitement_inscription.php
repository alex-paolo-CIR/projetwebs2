<?php
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
				$nom = $_POST["nom"];
				$prenom = $_POST["prenom"];
				$email = $_POST["email"];
				$password = password_hash($_POST["password"], PASSWORD_BCRYPT);

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