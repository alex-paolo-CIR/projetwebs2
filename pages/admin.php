<?php
// admin php page d administration

// le fichier de connexion doit etre inclus ici
require_once '../traitements/db.php'; // assure toi que le chemin est correct et que conn est bien l objet pdo

// --- securite basique a ameliorer fortement pour une vraie application ---
// assure toi que la session est demarree si tu utilises la securite basee sur les sessions
/*
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // rediriger vers la page de connexion ou afficher un message d erreur
    header('Location: login.php'); // adapte le nom de ta page de connexion
    exit;
}
*/
// --- fin securite ---

// --- recuperation des donnees ---

// 1 utilisateurs
try {
    // utilise conn si c est le nom de ta variable pdo dans db php
    $stmt_users = $conn->query("SELECT id, nom, prenom, email, admin, date_creation FROM utilisateurs ORDER BY nom, prenom");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC); // bonne pratique de specifier le fetch mode
} catch (\PDOException $e) {
    $error_users = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage(); // message d erreur conserve sa casse/ponctuation
    $users = [];
}

// 2 commandes avec nom de l utilisateur
try {
    $stmt_orders = $conn->query("
        SELECT c.id, c.date_commande, c.prix_total, c.statut, u.nom AS user_nom, u.prenom AS user_prenom
        FROM commandes c
        JOIN utilisateurs u ON c.utilisateur_id = u.id
        ORDER BY c.date_commande DESC
    ");
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $error_orders = "Erreur lors de la récupération des commandes: " . $e->getMessage(); // message d erreur conserve sa casse/ponctuation
    $orders = [];
}

// 3 stocks avec nom du produit et taille
try {
    $stmt_stock = $conn->query("
        SELECT sp.id, p.nom AS produit_nom, t.nom AS taille_nom, sp.quantite, p.id as produit_id, t.id as taille_id
        FROM stock_produits sp
        JOIN produits p ON sp.produit_id = p.id
        JOIN tailles t ON sp.taille_id = t.id
        ORDER BY p.nom, t.id
    ");
    $stock = $stmt_stock->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $error_stock = "Erreur lors de la récupération des stocks: " . $e->getMessage(); // message d erreur conserve sa casse/ponctuation
    $stock = [];
}

// --- preparation des donnees pour la section stock avec regroupement par produit ---
$products_with_stock = [];
if (isset($stock) && is_array($stock)) {
    foreach ($stock as $item) {
        $product_id = $item['produit_id'];
        // si le produit n est pas encore dans notre tableau structure
        if (!isset($products_with_stock[$product_id])) {
            $products_with_stock[$product_id] = [
                'produit_nom' => $item['produit_nom'],
                'produit_id' => $item['produit_id'],
                'stock_details' => [] // tableau pour stocker les details taille quantite etc
            ];
        }
        // ajouter les details du stock courant taille quantite id du stock a ce produit
        $products_with_stock[$product_id]['stock_details'][] = [
            'stock_id' => $item['id'], // id de la ligne stock_produits important pour le form
            'taille_id' => $item['taille_id'],
            'taille_nom' => $item['taille_nom'],
            'quantite' => $item['quantite']
        ];
    }
}
// $products_with_stock contient maintenant les produits groupes avec leurs details de stock

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'Administration - Miaou3</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #1a1a1a;
            color: #e0e0e0;
        }
        h1, h2 {
            color: #ffffff;
            border-bottom: 2px solid #c0392b;
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        h1 {
             text-align: center;
             text-transform: uppercase;
             letter-spacing: 2px;
             margin-bottom: 40px;
        }
        section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #444;
            border-radius: 8px;
            background-color: #2c2c2c;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* style pour le conteneur de tableau scrollable */
        .scrollable-table-container {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #555;
            border-radius: 4px;
            margin-top: 15px;
        }
        /* style pour la barre de défilement webkit blink chrome edge safari */
         .scrollable-table-container::-webkit-scrollbar {
            width: 10px;
        }
        .scrollable-table-container::-webkit-scrollbar-track {
            background: #333;
            border-radius: 5px;
        }
        .scrollable-table-container::-webkit-scrollbar-thumb {
            background-color: #c0392b;
            border-radius: 5px;
            border: 2px solid #333;
        }
         /* style pour la barre de défilement firefox */
        .scrollable-table-container {
            scrollbar-width: thin;
            scrollbar-color: #c0392b #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #555;
            padding: 10px 12px;
            text-align: left;
            color: #e0e0e0;
            vertical-align: middle;
        }
        th {
            background-color: #3f3f3f;
            color: #ffffff;
            position: sticky; /* rend l en tete fixe lors du scroll dans le conteneur */
            top: 0; /* necessaire pour sticky */
            z-index: 1; /* pour s assurer qu il est au dessus du contenu qui defile */
        }
        tr:nth-child(even) { background-color: #252525; }
        tr:hover { background-color: #4a4a4a; }

        .actions a, .actions button {
            margin-right: 5px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: inline-block;
            border: none;
            color: white;
        }
        .actions a:hover, .actions button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .actions .edit { background-color: #e67e22; } /* orange */
        .actions .delete { background-color: #c0392b; } /* rouge sombre */
        .actions .view { background-color: #2980b9; } /* bleu */
        .actions button.edit { /* s applique specifiquement au bouton maj stock */
             background-color: #27ae60; /* vert */
        }

        .error {
             color: #e74c3c;
             background-color: rgba(192, 57, 43, 0.1);
             border: 1px solid #c0392b;
             padding: 10px;
             border-radius: 4px;
             font-weight: bold;
             margin-bottom: 15px;
        }
        .stock-form { display: flex; align-items: center; } /* aligne input et bouton */
        .stock-form input[type="number"] {
            width: 70px;
            margin-right: 8px;
            padding: 4px 6px;
            background-color: #444;
            color: #fff;
            border: 1px solid #666;
            border-radius: 3px;
        }
        .stock-form input[type="number"]:focus {
             outline: none;
             border-color: #c0392b;
        }
        .stock-form button {
             padding: 4px 8px;
        }
        /* ajout d emoji pour admin status */
        td span.status-admin { color: #2ecc71; font-weight: bold; } /* vert pour admin oui */
        td span.status-not-admin { color: #e74c3c; font-weight: bold; } /* rouge pour admin non */


                /* --- styles pour les modals --- */
                .modal-toggle {
            display: none; /* cache la checkbox */
        }

        .modal {
            position: fixed; /* se positionne par rapport a la fenetre */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* fond semi transparent */
            display: none; /* cache par defaut */
            align-items: center; /* centre verticalement */
            justify-content: center; /* centre horizontalement */
            z-index: 1000; /* au dessus du reste */
            opacity: 0; /* pour transition */
            visibility: hidden; /* pour transition */
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-toggle:checked + .modal {
            display: flex; /* affiche le modal quand la checkbox est cochee */
            opacity: 1;
            visibility: visible;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer; /* indique qu on peut cliquer pour fermer */
            z-index: 1; /* sous le contenu mais au dessus du fond */
        }

        .modal-content {
            position: relative; /* pour positionner le bouton close */
            background-color: #2c2c2c;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 700px; /* largeur max du contenu */
            z-index: 2; /* au dessus de l overlay */
            max-height: 80vh; /* hauteur max pour eviter debordement */
            display: flex; /* utiliser flex pour organiser le contenu interne */
            flex-direction: column; /* elements internes en colonne */
        }

        .modal-content h3 {
            margin-top: 0;
            color: #ffffff;
            border-bottom: 1px solid #c0392b;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            line-height: 1; /* eviter hauteur excessive */
        }
        .modal-close-btn:hover {
            color: #fff;
        }

        /* conteneur pour le tableau dans le modal pour scroll si necessaire */
        .modal-table-container {
            overflow-y: auto; /* scroll si contenu depasse max height */
            max-height: 60vh; /* limite la hauteur du tableau */
            margin-top: 10px;
            border: 1px solid #555;
            border-radius: 4px;
        }

         /* style pour le tableau dans le modal */
        .modal-content table {
             width: 100%;
             border-collapse: collapse;
        }
         .modal-content th, .modal-content td {
            border: 1px solid #555;
            padding: 8px 10px;
            text-align: left;
            color: #e0e0e0;
            vertical-align: middle;
         }
         .modal-content th {
             background-color: #3f3f3f;
             color: #ffffff;
             position: sticky; /* en tete fixe DANS le scroll du modal */
             top: 0;
             z-index: 1;
         }
         .modal-content tr:nth-child(even) { background-color: #252525; }
         .modal-content tr:hover { background-color: #4a4a4a; }

        /* ajustement formulaire dans le modal */
        .stock-form-modal {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* aligner a gauche */
        }
        .stock-form-modal input[type="number"] {
             width: 60px; /* un peu moins large si besoin */
             margin-right: 5px;
             padding: 4px;
        }
        .stock-form-modal button {
             padding: 4px 8px;
             font-size: 0.85em; /* bouton un peu plus petit */
        }

        /* style pour le bouton ouvrir modal */
        label.actions.view {
            cursor: pointer;
             display: inline-block; /* important pour padding etc */
            background-color: #2980b9; /* style de bouton */
             color: white;
             padding: 5px 10px;
             border-radius: 4px;
             font-size: 0.9em;
             transition: background-color 0.2s ease, transform 0.1s ease;
        }
         label.actions.view:hover {
             opacity: 0.9;
             transform: translateY(-1px);
         }


    </style>
</head>
<body>

    <h1>PANNEL ADMIN</h1>

    <section id="users">
        <h2><i class="fas fa-users"></i> Gestion des Utilisateurs</h2>
        <?php if (isset($error_users)): ?>
            <p class="error"><?php echo htmlspecialchars($error_users); ?></p>
        <?php endif; ?>

        <!-- conteneur pour le tableau scrollable -->
        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Date Création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['admin']): ?>
                                <span class="status-admin" title="Administrateur">✔ Oui</span>
                            <?php else: ?>
                                <span class="status-not-admin" title="Utilisateur standard">✘ Non</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['date_creation']))); ?></td>
                        <td class="actions">
                            <a href="../traitements/mod_utilisateur.php?id=<?php echo $user['id']; ?>" class="edit" title="Modifier l'utilisateur">Modifier</a>
                            <a href="../traitements/supr_utilisateur.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?\nNom: <?php echo htmlspecialchars(addslashes($user['prenom'] . ' ' . $user['nom'])); ?>');" title="Supprimer l'utilisateur">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users) && !isset($error_users)): ?>
                    <tr><td colspan="7">Aucun utilisateur trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- fin du conteneur scrollable -->
        <!-- <a href="create_user.php" class="button add-button">Ajouter un utilisateur</a> -->
    </section>

    <section id="orders">
        <h2><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h2>
         <?php if (isset($error_orders)): ?>
            <p class="error"><?php echo htmlspecialchars($error_orders); ?></p>
        <?php endif; ?>

        <!-- conteneur pour le tableau scrollable -->
        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($order['date_commande']))); ?></td>
                        <td><?php echo htmlspecialchars($order['user_prenom'] . ' ' . $order['user_nom']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($order['prix_total'], 2, ',', ' ')); ?> €</td>
                        <td><?php echo htmlspecialchars($order['statut']); ?></td>
                        <td class="actions">
                            <a href="view_order.php?id=<?php echo $order['id']; ?>" class="view" title="Voir les détails de la commande">Détails</a>
                            <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="edit" title="Modifier le statut de la commande">Statut</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders) && !isset($error_orders)): ?>
                    <tr><td colspan="6">Aucune commande trouvée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- fin du conteneur scrollable -->
    </section>

        <!-- Section Gestion des Stocks Modifiée avec Modals -->
    <section id="stock">
        <h2><i class="fas fa-boxes"></i> Gestion des Stocks</h2>
         <?php if (isset($error_stock)): ?>
            <p class="error"><?php echo htmlspecialchars($error_stock); ?></p>
        <?php endif; ?>

         <!-- conteneur pour la table principale des produits -->
        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>Produit (ID)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products_with_stock as $product_id => $product_info): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product_info['produit_nom']); ?> (<?php echo htmlspecialchars($product_id); ?>)</td>
                        <td class="actions">
                            <!-- le label agit comme un bouton pour ouvrir le modal correspondant -->
                            <label for="modal-toggle-<?php echo $product_id; ?>" class="actions view" title="Gérer le stock de ce produit">Gérer Stock</label>
                            <a href="edit_product.php?id=<?php echo $product_id; ?>" class="actions edit" title="Voir/Modifier la fiche produit">Modifier Produit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products_with_stock) && !isset($error_stock)): ?>
                    <tr><td colspan="2">Aucun produit avec stock trouvé</td></tr> <!-- texte mis a jour -->
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- fin du conteneur scrollable de la table principale -->

        <!-- generation des modals caches pour chaque produit -->
        <?php foreach ($products_with_stock as $product_id => $product_info): ?>
            <!-- la checkbox cachee qui controle l etat ouvert ferme du modal -->
            <input type="checkbox" id="modal-toggle-<?php echo $product_id; ?>" class="modal-toggle">

            <!-- le modal lui meme -->
            <div class="modal">
                <!-- overlay cliquable pour fermer le modal -->
                <label for="modal-toggle-<?php echo $product_id; ?>" class="modal-overlay"></label>

                <!-- contenu du modal -->
                <div class="modal-content">
                    <!-- bouton de fermeture dans le coin -->
                    <label for="modal-toggle-<?php echo $product_id; ?>" class="modal-close-btn" title="Fermer">×</label>

                    <h3>Stock pour : <?php echo htmlspecialchars($product_info['produit_nom']); ?></h3>

                    <!-- tableau des stocks pour ce produit specifique -->
                    <div class="modal-table-container">
                         <table>
                            <thead>
                                <tr>
                                    <th>Taille (ID)</th>
                                    <th>Quantité Actuelle</th>
                                    <th>Mettre à Jour</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product_info['stock_details'] as $detail): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detail['taille_nom']); ?> (<?php echo htmlspecialchars($detail['taille_id']); ?>)</td>
                                    <td><?php echo htmlspecialchars($detail['quantite']); ?></td>
                                    <td>
                                        <!-- formulaire pour mettre a jour la quantite pour cette taille specifique -->
                                        <form action="update_stock.php" method="POST" class="stock-form-modal">
                                            <input type="hidden" name="stock_id" value="<?php echo htmlspecialchars($detail['stock_id']); ?>"> <!-- utilise l id de la ligne stock_produits -->
                                            <input type="number" name="quantite" value="<?php echo htmlspecialchars($detail['quantite']); ?>" min="0" required title="Nouvelle quantité">
                                            <button type="submit" class="actions edit" title="Mettre à jour la quantité">MàJ</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($product_info['stock_details'])): ?>
                                    <tr><td colspan="3">Aucune taille définie pour ce produit en stock</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- fin modal table container -->

                </div> <!-- fin modal content -->
            </div> <!-- fin modal -->
        <?php endforeach; ?>
         <!-- <a href="add_stock.php">Ajouter du stock globalement ou nouveau produit</a> -->
    </section>

</body>
</html>