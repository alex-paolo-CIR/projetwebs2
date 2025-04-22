<?php
if (isset($_POST["Envoyer"])) {
    try {
        require("db.php");

        // Vérifier si la méthode est bien POST
        if ($_SERVER["REQUEST_METHOD"] != "POST")
            header("location:../pages/ajouter_produit.php");

        // Vérifier si les champs obligatoires sont remplis
        if (
            empty($_POST["nom"]) || 
            empty($_POST["prix"]) || 
            empty($_POST["categorie_id"])
        ) {
            header("location:../pages/ajouter_produit.php?error=empty");
            exit();
        }

        $nom = $_POST["nom"];
        $description = isset($_POST["description"]) ? $_POST["description"] : null;
        $prix = $_POST["prix"];
        $categorie_id = $_POST["categorie_id"];
        $a_des_tailles = isset($_POST["a_des_tailles"]) ? 1 : 0;

        // Traiter l'image principale
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Valider l'image principale
            $image = $_FILES['image'];
            $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $imageExtension = strtolower($imageExtension);

            if (in_array($imageExtension, ['jpg', 'jpeg', 'png'])) {
                $imageName = $nom . '.' . $imageExtension;
                $imagePath = "../images/" . $imageName;

                // Déplacer le fichier image dans le dossier images
                if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                    die("Erreur lors de l'upload de l'image principale.");
                }
            } else {
                die("L'image principale n'est pas valide. Assurez-vous qu'elle soit au format JPG, JPEG ou PNG.");
            }
        } else {
            die("Aucune image principale envoyée.");
        }

        // Traiter l'image hover
        if (isset($_FILES['image_hover']) && $_FILES['image_hover']['error'] === 0) {
            // Valider l'image hover
            $imageHover = $_FILES['image_hover'];
            $imageHoverExtension = pathinfo($imageHover['name'], PATHINFO_EXTENSION);
            $imageHoverExtension = strtolower($imageHoverExtension);

            if (in_array($imageHoverExtension, ['jpg', 'jpeg', 'png'])) {
                $imageHoverName = $nom . '_hover.' . $imageHoverExtension;
                $imageHoverPath = "../images/" . $imageHoverName;

                // Déplacer le fichier image hover dans le dossier images
                if (!move_uploaded_file($imageHover['tmp_name'], $imageHoverPath)) {
                    die("Erreur lors de l'upload de l'image hover.");
                }
            } else {
                die("L'image hover n'est pas valide. Assurez-vous qu'elle soit au format JPG, JPEG ou PNG.");
            }
        } else {
            die("Aucune image hover envoyée.");
        }

        // Insérer les informations dans la base de données
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

        // Récupérer l'ID du produit inséré
        $produit_id = $conn->lastInsertId();

        // Gérer les tailles
        if ($a_des_tailles) {
            $reqTailles = "SELECT id FROM tailles WHERE nom != 'Unique'";
        } else {
            $reqTailles = "SELECT id FROM tailles WHERE nom = 'Unique'";
        }

        $stmtTailles = $conn->prepare($reqTailles);
        $stmtTailles->execute();
        $tailles = $stmtTailles->fetchAll(PDO::FETCH_COLUMN);

        // Insérer les tailles du produit
        $reqStock = "INSERT INTO stock_produits (produit_id, taille_id, quantite) VALUES (:produit_id, :taille_id, 0)";
        $stmtStock = $conn->prepare($reqStock);

        foreach ($tailles as $taille_id) {
            $stmtStock->execute([
                ":produit_id" => $produit_id,
                ":taille_id" => $taille_id
            ]);
        }

        // Fermer la connexion et rediriger
        $conn = NULL;
        header("location:../pages/ajouter_item.php?success=1");

    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>
