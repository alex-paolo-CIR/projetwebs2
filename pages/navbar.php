

<nav class="navbar">
    <div class="utils-co">
        <?php if (isset($_SESSION["authentifie"]) && $_SESSION["authentifie"] === true): ?>
            <a href="profil.php">
                <img id="connexion" class="icones" src="../media/icon-account.png" alt="icon-account">
            </a>
            <a href="../traitements/logout.php" class="deco" style="
            /* décaler vers la droite */
            margin-left: 10px;
            ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="red" width="24" height="24" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                </svg>
            </a>

            <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                <a href="admin.php" class="admin-button">Admin</a>
            <?php endif; ?>
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
        </button>
    </div>

    <nav popover id="cart">
        <button popovertarget="cart" popovertargetaction="hide" class="button close-button">×</button>
        <div class="cart-item">
        <img src="../media/merch/vinyl1.png" alt="Vinyl 2">
        <div class="item-details">
            <p>ROCKSTAR Vinyle EDITION DELUXE</p>
            <p>Prix: 39,99 €</p>
        </div>
        </div>
        <div class="cart-item">
        <img src="../media/merch/Pull_noir_devant.png" alt="Pull 1">
        <div class="item-details">
            <p>Pull Noir Msd</p>
            <p>Prix: 74,99 €</p>
            <p>Taille: L</p>
        </div>
        </div>
        <div class="cart-item">
        <img src="../media/merch/affiche-normall.png" alt="Poster 2">
        <div class="item-details">
            <p>Pack de poster Msd x3</p>
            <p>Prix: 19,99 €</p>
        </div>
        </div>
        <div class="cart-item">
        <img src="../media/merch/Teeshirt_b_devant.png" alt="T-shirt 2">
        <div class="item-details">
            <p>T-shirt blanc Msd</p>
            <p>Prix: 24,99 €</p>
            <p>Taille: XL</p>
        </div>
        </div>
        <div class="checkout">
        <p>Total: 159,96 €</p>
        <button>Procéder au paiement</button>
        </div>
    </nav>


    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <div class="navbar-menu">
    <a href="accueil.php" class="<?= $currentPage == 'accueil.php' ? 'active' : '' ?>">ACCUEIL</a>
    <a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">BOUTIQUE</a>
    <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">CONTACT</a>
    </div>
</nav>