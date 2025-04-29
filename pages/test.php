<?php
function nettoyer_donnees($donnees){
    $donnees = trim($donnees);
    $donnees = stripslashes($donnees);
    $donnees = htmlspecialchars($donnees);
    return $donnees;    
}

$version = nettoyer_donnees("OK LOL lol é'-è123456");
$version1 = nettoyer_donnees("Alexandre");

function valider_NomPrenom($NomPrenom){
    return preg_match("/^[a-zA-Z\s'-]{1,40}$/",$NomPrenom);
}


echo "La version du php est : $version <br>";
echo "La version du php est : ". valider_NomPrenom($version)." <br>";
echo "La version du php est : ". valider_NomPrenom($version1)." <br>";
?>