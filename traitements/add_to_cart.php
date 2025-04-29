<?php
session_start();

if (!empty($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 0;
    }
    $_SESSION['cart'][$product_id]++;
    
    $_SESSION['notification'] = "Produit ajouté au panier avec succès !";
}

header('Location: ../pages/shop.php');
exit();
?>
