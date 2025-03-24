<?php

	if(isset($_POST["Ajouter"])){
		try{
			require("connexion.php");               
			
			//Compléter ICI

			// Tests bouton envoyer et méthode POST
			if (!isset($_POST['Ajouter']) || $_SERVER['REQUEST_METHOD'] != 'POST')
				header('location:../html/inscription.php');

			// Tests si champs vides
			if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['password']) )
				header('location:../html/inscription.php');

			$nom = $_POST["nom"];
			$prenom = $_POST["prenom"];
			$email = $_POST["email"];
			$password = password_hash($_POST["password"], PASSWORD_BCRYPT);

			$reqPrep= "INSERT INTO user VALUES (NULL, :nom, :prenom, :email, :password)";//La requete SQL INSERT INTO 

			// preparation de la requete avec marqueur nommés
			$req1 = $conn->prepare($reqPrep);

			//execution de la requete preparée 
			$req1->execute(array(':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':password' => $password));


			$conn= NULL;
			header("location:../html/accueil.html");
		}                 
		catch(Exception $e){
			die("Erreur : " . $e->getMessage());
        }
	}

?>