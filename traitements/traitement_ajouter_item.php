<?php
if (isset($_POST["Envoyer"])) {
    try {
        require("db.php");

        // verifier si la méthode est bien POST
        if ($_SERVER["REQUEST_METHOD"] != "POST")
            header("location:../pages/admin.php");

        // verifier si les champs obligatoires sont remplis
        if (
            empty($_POST["nom"]) || 
            empty($_POST["prix"]) || 
            empty($_POST["categorie_id"])
        ) {
            header("location:../pages/admin.php?error=empty");
            exit();
        }

        $nom = $_POST["nom"];
        $description = isset($_POST["description"]) ? $_POST["description"] : null;
        $prix = $_POST["prix"];
        $categorie_id = $_POST["categorie_id"];

        if($_POST["a_des_tailles"] == "1"){
            $a_des_tailles = 1;
        }else{
            $a_des_tailles = 0;
        }

        function valider_Photo($fichier) {
            if (!isset($fichier['name']) || $fichier['error'] !== UPLOAD_ERR_OK) {
                return false;
            }
            $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
            return in_array($extension, ['jpg', 'jpeg', 'png']);
        }

        $vPhoto = $_FILES['image'];
        $vPhotoHover = $_FILES['image_hover'];
        
        if (!valider_Photo($vPhoto)) {
            header("location:../pages/admin.php?error=imageclassic");
            exit();
        }
        
        if (!valider_Photo($vPhotoHover)) {
            header("location:../pages/admin.php?error=imagehover");
            exit();
        }

        //img classique
        $extension = strtolower(pathinfo($vPhoto['name'], PATHINFO_EXTENSION));
        $imageName = $nom . '_image.' . $extension;
        move_uploaded_file($vPhoto['tmp_name'], "../media/merch/$imageName");
        
        //hover
        $extensionHover = strtolower(pathinfo($vPhotoHover['name'], PATHINFO_EXTENSION));
        $imageHoverName = $nom . '_image_hover.' . $extensionHover;
        move_uploaded_file($vPhotoHover['tmp_name'], "../media/merch/$imageHoverName");
        



        // inserer les informations dans la base de données
        $reqInsertProduit = "INSERT INTO produits (nom, description, prix, image, image_hover, categorie_id, a_des_tailles)
                             VALUES (:nom, :description, :prix, :image, :image_hover, :categorie_id, :a_des_tailles)";
        $stmtProduit = $conn->prepare($reqInsertProduit);
        $stmtProduit->execute([
            ":nom" => $nom,
            ":description" => $description,
            ":prix" => $prix,
            ":image" => $imageName,
            ":image_hover" => $imageHoverName,
            ":categorie_id" => $categorie_id,
            ":a_des_tailles" => $a_des_tailles
        ]);

        // recupérer l'ID du produit inséré
        $produit_id = $conn->lastInsertId();

        // gérer les tailles
        if ($a_des_tailles) {
            $reqTailles = "SELECT id FROM tailles WHERE nom != 'Unique'";
        } else {
            $reqTailles = "SELECT id FROM tailles WHERE nom = 'Unique'";
        }

        $stmtTailles = $conn->prepare($reqTailles);
        $stmtTailles->execute();
        $tailles = $stmtTailles->fetchAll(PDO::FETCH_COLUMN);

        // insérer les tailles du produit
        $reqStock = "INSERT INTO stock_produits (produit_id, taille_id, quantite) VALUES (:produit_id, :taille_id, 0)";
        $stmtStock = $conn->prepare($reqStock);

        foreach ($tailles as $taille_id) {
            $stmtStock->execute([
                ":produit_id" => $produit_id,
                ":taille_id" => $taille_id
            ]);
        }

        // fermer la connexion et rediriger
        $conn = NULL;
        header("location:../pages/admin.php?success=1");

    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>
