<?php
try {
    require("../traitements/db.php");

    // Récupérer tous les produits
    $req = "SELECT * FROM produits";
    $stmt = $conn->prepare($req);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>MSD - Boutique</title>
    <link rel="icon" type="image/x-icon" href="../media/favicon.ico">
    <link rel="stylesheet" type="text/css" href="../style/main.css">
    <link rel="stylesheet" type="text/css" href="../style/navbar.css">
    <link rel="stylesheet" type="text/css" href="../style/shop.css">

    <style>
        <?php foreach ($produits as $index => $produit): 
            $image_hover = htmlspecialchars($produit['image_hover']);
        ?>
        .item:nth-child(<?= $index + 1 ?>):hover img {
            content: url("../media/merch/<?= $image_hover ?>");
        }
        <?php endforeach; ?>
    </style>
</head>

<body>
    <nav class="navbar">
        <!-- navbar raccourcie pour lisibilité -->
    </nav>

    <div class="conteneur-shop">
        <div class="shop-grid">
            <?php
            foreach ($produits as $index => $produit):
                $nom = htmlspecialchars($produit['nom']);
                $prix = number_format($produit['prix'], 2);
                $image = htmlspecialchars($produit['image']);
            ?>
                <div class="item" id="item<?= $index + 1 ?>">
                    <a href="#modal<?= $index + 1 ?>">
                        <img src="../media/merch/<?= $image ?>" alt="<?= $nom ?>">
                        <p><?= $nom ?> - <?= $prix ?>€</p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    foreach ($produits as $index => $produit):
        $nom = htmlspecialchars($produit['nom']);
        $image = htmlspecialchars($produit['image']);
        $description = htmlspecialchars($produit['description']);
        $prix = number_format($produit['prix'], 2);
    ?>
        <div id="modal<?= $index + 1 ?>" class="modal">
            <div class="modal-content">
                <a href="#item<?= $index + 1 ?>" class="close">×</a>
                <div class="modal-layout">
                    <div class="agauche">
                        <img src="../media/merch/<?= $image ?>" alt="<?= $nom ?>" class="modal-image">
                    </div>
                    <div class="modal-info">
                        <h2><?= $nom ?></h2>
                        <p class="description"><?= $description ?></p>
                        <p class="price"><?= $prix ?>€</p>
                        <div class="size-selector">
                            <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                <input type="radio" id="size-<?= strtolower($size) ?>-<?= $index + 1 ?>" name="size-<?= $index + 1 ?>" value="<?= $size ?>" hidden>
                                <label for="size-<?= strtolower($size) ?>-<?= $index + 1 ?>" class="size-option"><?= $size ?></label>
                            <?php endforeach; ?>
                        </div>
                        <button class="add-to-cart">
                            <span class="button-text">Ajouter au panier</span>
                            <img class="check-icon" src="../media/icon-check.png" alt="Check">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 MSD. TOUS DROITS RÉSERVÉS.</p>
            <div class="socials">
                <a href="https://soundcloud.com/msdmsd" class="social-icon">SoundCloud</a>
                <a href="https://www.youtube.com/@Msd-Prime" class="social-icon">YouTube</a>
                <a href="https://www.instagram.com/msdprod_/?next=%2Fmars.620%2F" class="social-icon">Instagram</a>
            </div>
        </div>
    </footer>
</body>
</html>
