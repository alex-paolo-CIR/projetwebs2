<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification CSS Pure</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
body {
    font-family: sans-serif;
    margin: 0;
    padding-top: 60px;
    position: relative;
}

/* Style du "bouton" déclencheur */
.bouton-declencheur {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    user-select: none;
    margin: 20px;
}

.bouton-declencheur:hover {
    background-color: #0056b3;
}

/* La case à cocher réelle est cachée */
.toggle-notif-input {
    display: none;
}

/* Style de base de la notification */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;

    background-color: #4CAF50; /* Vert */
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);

    /* Initialement complètement cachée et prête pour l'animation */
    opacity: 0;
    visibility: hidden; /* Important pour ne pas interagir quand invisible */
    transform: translateY(-150%);

    /* On NE met PAS de transition ici, l'animation s'en charge */
}

.notification-contenu {
    position: relative;
    padding-right: 25px;
}

/* Style du bouton fermer */
.notification-fermer {
    position: absolute;
    top: 50%;
    right: 0px;
    transform: translateY(-50%);
    font-size: 24px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    padding: 0 5px;
    line-height: 1;
}

.notification-fermer:hover {
    opacity: 0.8;
}

/* --- Définition de l'animation --- */
@keyframes notification-animation {
    /* Étape 1: Apparition (par exemple sur 0.4s) */
    0% {
        opacity: 0;
        transform: translateY(-150%);
        visibility: visible; /* Rendre visible pour que l'anim fonctionne */
    }
    10% { /* 0.4s / (0.4s + 3s + 0.4s) = 0.4 / 3.8 ~= 10% */
        opacity: 1;
        transform: translateY(0);
    }

    /* Étape 2: Maintien visible (pendant 3s) */
    /* Reste à opacity: 1 et transform: translateY(0) jusqu'à la fin des 3s */
    /* (0.4s + 3s) / 3.8s = 3.4 / 3.8 ~= 89% */
    89% {
        opacity: 1;
        transform: translateY(0);
    }

    /* Étape 3: Disparition (par exemple sur 0.4s) */
    100% {
        opacity: 0;
        transform: translateY(-150%);
        visibility: hidden; /* Cacher à la toute fin */
    }
}

/* --- Application de l'animation quand la case est cochée --- */
.toggle-notif-input:checked ~ .notification {
    /* Déclenche l'animation */
    animation-name: notification-animation;
    /* Durée totale: apparition + visible + disparition */
    animation-duration: 3.8s; /* 0.4s (in) + 3s (visible) + 0.4s (out) */
    animation-timing-function: ease-out; /* Courbe de vitesse */
    animation-fill-mode: forwards; /* Garde l'état de la dernière keyframe (caché) */
    animation-iteration-count: 1; /* L'animation ne joue qu'une fois */
}

/* Style pour le contenu de la page (juste pour l'exemple) */
.contenu-page {
    padding: 20px;
    margin-top: 30px;
    border: 1px solid #ccc;
}
</style>
<body>

    <!-- La case à cocher cachée qui contrôle l'état -->
    <input type="checkbox" id="toggle-notif" class="toggle-notif-input">

    <!-- Le "bouton" (en réalité une étiquette pour la case à cocher) -->
    <label for="toggle-notif" class="bouton-declencheur">
        Afficher la Notification
    </label>

    <!-- La notification elle-même -->
    <div class="notification" id="ma-notification">
        <div class="notification-contenu">
            Ceci est une notification de succès !
            <!-- Bouton/lien pour fermer (une autre étiquette pour la même case) -->
            <label for="toggle-notif" class="notification-fermer">×</label>
        </div>
    </div>

    <div class="contenu-page">
        <h1>Contenu principal de la page</h1>
        <p>Le reste de votre page est ici...</p>
    </div>

</body>
</html>