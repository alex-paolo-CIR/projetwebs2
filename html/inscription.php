<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device.width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>MSD - Inscription</title>
    <link rel="icon" type="image/x-icon" href="../media/favicon.ico">
    <link rel="stylesheet" type="text/css" href="../style/main.css">
    <link rel="stylesheet" type="text/css" href="../style/navbar.css">
    <link rel="stylesheet" type="text/css" href="../style/connexion.css">

</head>

<body>
    <section class="fond">
        <video autoplay loop muted playsinline class="video-webm">
            <source src="../media/fond.webm" type="video/webm">
        </video>

        <div class="overlay">

            <nav class="navbar">

                <div class="logo">
                    <a href="../index.html"><img src="../media/logo_msd.png" alt="Logo"></a>
                </div>

                <div class="navbar-menu">
                    <a href="accueil.html">ACCUEIL</a>
                    <a href="shop.html">BOUTIQUE</a>
                    <a href="contact.html">CONTACT</a>
                </div>
            </nav>

            <div class="conteneur-login-global">
                <div class="conteneur-login">
                    <form action="track.html" method="get">

                        <h1>Inscription</h1>

                        <div class="input-box">
                            <input type="text" placeholder="Nom d'utilisateur" required>
                            <img id="user" src="../media/icon-account.png" alt="icon-account">
                        </div>

                        <div class="input-box">
                            <input type="email" placeholder="Adresse e-mail" required>
                            <img id="mail" src="../media/icon-mail.png" alt="icon-mail">
                        </div>

                        <div class="input-box">
                            <input type="password" placeholder="Mot de passe" required>
                            <img id="pwd" src="../media/icon-lock.png" alt="icon-lock">
                        </div>

                        <button type="submit" class="btn">S'inscrire</button>

                        <div class="inscription">
                            <p>Déjà un compte ? <a href="connexion.html">Se connecter</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>



    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 MSD. TOUT DROITS RESERVES.</p>
            <div class="socials">
                <a href="https://soundcloud.com/msdmsd" class="social-icon">SoundCloud</a>
                <a href="https://www.youtube.com/@Msd-Prime" class="social-icon">YouTube</a>
                <a href="https://www.instagram.com/msdprod_/?next=%2Fmars.620%2F" class="social-icon">Instagram</a>
            </div>
        </div>
    </footer>
</body>

</html>