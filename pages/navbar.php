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
    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
    <div class="navbar-menu">
        <a href="accueil.php" class="<?= $currentPage == 'accueil.php' ? 'active' : '' ?>">ACCUEIL</a>
        <a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">BOUTIQUE</a>
        <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">CONTACT</a>
    </div>
</nav>
