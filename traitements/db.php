<?php
	$servername = "localhost"; 
	$username = "root"; 
	$password ="root"; 
	$database = "projetws2"; // miaou3 pour tests de julio 
	
	$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
?>