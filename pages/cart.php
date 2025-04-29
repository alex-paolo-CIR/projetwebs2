<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->
<!-- CETTE PAGE EST UNE PAGE DEV POUR OBSERVER LES PB ETC DONC JE LA SUPPRIME APRES -->



<?php
session_start();
require_once '../traitements/db.php'; 

echo "<h1>Votre Meeeeeoowwwnier</h1>";

if (!empty($_SESSION['cart'])) {
    $total = 0;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $subtotal = $product['prix'] * $quantity;
            $total += $subtotal;
            echo "<div class='cart-item'>";
            echo "<p><strong>{$product['nom']}</strong></p>";
            echo "<p>Prix: " . number_format($product['prix']) . " €</p>";
            echo "<p>Quantité: $quantity</p>";
            echo "<p>Total de ce produit: " . number_format($subtotal) . " €</p>";
            echo "<p>Taille du produit: {$product['taille']}</p>";
            echo "<p>Image du produit: <img src='../media/merch/{$product['image']}' alt='{$product['nom']}' style='width: 100px;'></p>";   
            echo "<hr>";
            echo "</div>";
        }
    }

    echo "<h2>Total du panier : " . number_format($total) . " €</h2> <h2>(ca fait bcp pour ca miaouuu)</h2>";
} else {
    echo "<p>Votre panier est vide.</p>";
}
?>
