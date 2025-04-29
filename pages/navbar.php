<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<


<nav class="navbar">
    <div class="utils-co">
        <?php if (isset($_SESSION["authentifie"]) && $_SESSION["authentifie"] === true): ?>
            <a href="profil.php">
                <img id="connexion" class="icones" src="../media/icon-account.png" alt="icon-account">
            </a>
            <div class="gest">
                <a href="../traitements/logout.php" class="deco" style="margin-left: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="red" width="24" height="24" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                </svg>
            </a>
            <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                <a href="admin.php">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="17.5" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#FFD43B" d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7l131.7 0c0 0 0 0 .1 0l5.5 0 112 0 5.5 0c0 0 0 0 .1 0l131.7 0c17 0 30.7-13.8 30.7-30.7c0-75.5-51.9-138.9-121.9-156.4c-8.1-2-15.9 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2L224 304l-19.7 0c-12.4 0-20.1 13.6-13.7 24.2z"/></svg>
                </a>
            <?php endif; ?>
            </div>
            
        <?php else: ?>
            <a href="connexion.php">
                <img id="connexion" class="icones" src="../media/icon-account-d.png" alt="icon-account">
            </a>
        <?php endif; ?>
    </div>
    <div class="logo">
        <a href="../index.html">
            <img src="../media/logo_msd.png" alt="Logo">
        </a>
    </div>
    <div class="utils-ca">
        <button popovertarget="cart" popovertargetaction="show" class="button">
            <img id="panier" class="icones" src="../media/icon-cart.png" alt="Panier">
            <?php
                if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    $total_items = 0;
                    foreach ($_SESSION['cart'] as $product_id => $quantity) {
                        if ($quantity > 0) {
                            $total_items += $quantity;
                        }
                    }
                    if ($total_items > 0) {
                        echo '<span class="cart-counter">' . $total_items . '</span>';
                    }
                }
?>

        </button>
    </div>
    <nav popover id="cart">
        <button popovertarget="cart" popovertargetaction="hide" class="button close-button">×</button>
        <?php
    require_once '../traitements/db.php';
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $total = 0;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if ($product) {
                $subtotal = $product['prix'] * $quantity;
                $total += $subtotal;
                echo "<div class='cart-item'>"; 
                echo "<div class='cart-header'>";
                echo "<form method='POST' action='../traitements/remove_from_cart.php' class='inline-form'>";
                echo "<input type='hidden' name='product_id' value='$product_id'>";
                echo "<button type='submit' class='delete-icon' title='Supprimer'>×</button>";
                echo "</form>";
                echo "</div>";
                echo "<img src='../media/merch/{$product['image']}' alt='{$product['nom']}' style='width:50px;height:50px;'>";
                echo "<div class='item-details'>";
                echo "<p><strong>" . htmlspecialchars($product['nom']) . "</strong></p>";
                echo "<p>Prix: " . number_format($product['prix'], 2) . " €</p>";
                echo "<p>Quantité: $quantity</p>";
                echo "<p>Taille: {$product['taille']}</p>";
                echo "<p>Total: " . number_format($subtotal, 2) . " €</p>";
                echo "</div></div>";


                

            }
        }
        echo "<div class='checkout'>";
        echo "<p><strong>Total: " . number_format($total, 2) . " €</strong></p>";
        echo "<a href='cart.php'><button>Voir mon panier</button></a>";
        echo "</div>";
    } else {
        echo "<p>Votre panier est vide.</p>";
    }
    ?>

    <style>
            .cart-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 0 5px 0;
            }

            .remove-form {
            margin: 0;
            }

            .delete-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: transparent;
            border: 2px solid yellow;
            color: yellow;
            font-size: 14px;
            font-weight: bold;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            margin-left: 10px;
            transition: background-color 0.2s, color 0.2s, border-color 0.2s;
            }

            .delete-icon:hover {
            background-color: yellow;
            color: black;
            border-color: yellow;
            }


    </style>
    </nav>
    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
    <div class="navbar-menu">
        <a href="accueil.php" class="<?= $currentPage == 'accueil.php' ? 'active' : '' ?>">ACCUEIL</a>
        <a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">BOUTIQUE</a>
        <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">CONTACT</a>
    </div>
</nav>


 <style> 
    /* css du popover pour le moment ici */
    nav#cart {
    padding: 20px;
    width: 300px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    color: black;
}

</style>



<style>
    .cart-counter {
    background-color: yellow;
    color: black;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    padding: 3px 8px;
    /* position: absolute; */
    /* top: 5px;
    right: 5px; */
    transform: translate(50%, -50%);
    z-index: 1001;
}



</style>
