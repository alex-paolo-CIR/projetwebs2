<?php
// admin php page d administration

// le fichier de connexion doit etre inclus ici
require_once '../traitements/db.php'; // assure toi que le chemin est correct et que conn est bien l objet pdo

// --- securite basique a ameliorer fortement pour une vraie application ---
// assure toi que la session est demarree si tu utilises la securite basee sur les sessions

session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header('Location: accueil.php'); // redirige vers la page d accueil si pas admin
    exit;
}

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
    <link rel="stylesheet" href="../style/admin.css">
    <title>Panneau d'Administration - Miaou3</title>
</head>
<body>

    <h1>PANNEL ADMIN</h1>

    <!-- RETOUR A LACCUEIL -->
    <a href="./accueil.php" style="text-decoration:none; color: #FFF; margin: 0;
     /* écarter en hauteur de gestion  */
        padding: 10px 20px; background-color:rgba(117, 117, 117, 0.87); border-radius: 5px; font-size: 1.2em; display: inline-block; margin-bottom: 20px;">Retour à l'accueil</a>


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

    <section id="ajouter_item">

    <h1>Ajouter un nouvel item au shop</h1>

<form action="../traitements/traitement_ajouter_item.php" method="POST"enctype="multipart/form-data">
  <label for="nom">Nom :</label><br>
  <input type="text" id="nom" name="nom" required><br><br>

  <label for="description">Description :</label><br>
  <textarea id="description" name="description" required></textarea><br><br>

  <label for="prix">Prix (€) :</label><br>
  <input type="number" step="0.01" id="prix" name="prix" required><br><br>

  <label for="image">Image :</label><br>
  <input type="file" id="image" name="image" accept="image/*"><br><br>

  <label for="image_hover">Image Hover :</label><br>
  <input type="file" id="image_hover" name="image_hover" accept="image/*"><br><br>

  <label for="categorie_id">Catégorie :</label><br>
  <select id="categorie_id" name="categorie_id" required>
    <option value="1">Vêtements</option>
    <option value="2">CD</option>
    <option value="3">Vinyles</option>
    <option value="4">Accessoires</option>
  </select><br><br>


  <label for="a_des_tailles">A des tailles ?</label><br>
  <select id="a_des_tailles" name="a_des_tailles" required>
    <option value="1">Oui</option>
    <option value="0">Non</option>
  </select><br><br>

  <button type="submit" name="Envoyer">Ajouter l'item</button>
</form>


<?php
              // Vérification des erreurs dans l'URL
              if (isset($_GET['success'])) {
                  $success = $_GET['success'];
                  $successMessage = '';
                  if ($success == '1') {
                      $successMessage = "Item ajouté avec succès !";
                  }
              }
          ?>

          <!-- Div contenant le message d'erreur si nécessaire -->
          <?php if (!empty($successMessage)): ?>
              <div class="success-message">
                  <?php echo $successMessage; ?>
              </div>
          <?php endif; ?>





    </section>
</body>
</html>