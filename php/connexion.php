<?php	
	
	$servername = "localhost"; 
	$username = "root"; 
	$password ="root"; 
	$database = "projetws2";
	
	$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	
	
?>