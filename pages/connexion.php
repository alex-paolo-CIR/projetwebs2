<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device.width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>MSD - Connexion</title>
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


            <?php
                // Vérification des erreurs dans l'URL
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $errorMessage = '';

                    if ($error == 'empty') {
                        $errorMessage = "Veuillez remplir tous les champs.";
                    } elseif ($error == 'mdp') {
                        $errorMessage = "Mot de passe incorrect.";
                    } elseif ($error == 'email') {
                        $errorMessage = "Email invalide";
                    }
                }
            ?>








            <div class="conteneur-login-global">

        <!-- Div contenant le message d'erreur si nécessaire -->
             <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            <style>
                .error-message {
                    position: flex;
                    color: red;
                    background-color: #f8d7da;
                    border: 1px solid #f5c6cb;
                    padding: 10px;
                    margin-bottom: 19%;
                    border-radius: 5px;
                    text-align: center;
                }
            </style>
               

                <div class="conteneur-login">
                    <form action="../traitements/traitement_connexion.php" method="post">

                        <h1>Connexion</h1>

                        <div class="input-box">
                            <input type="email" placeholder="Email" name="email" required>
                            <img id="user" src="../media/icon-mail.png" alt="icon-account">
                        </div>

                        <div class="input-box">
                            <input type="password" placeholder="Mot de passe" name="password" required>
                            <img id="pwd" src="../media/icon-lock.png" alt="icon-lock">
                        </div>

                        <button type="submit" class="btn" name="Connexion" >Se connecter</button>

                        <div class="inscription">
                            <p>Vous n'avez pas de compte ? <a href="inscription.php">S'inscrire</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>



    

</body>

</html>