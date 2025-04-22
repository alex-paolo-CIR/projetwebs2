<?php
// admin.php - page d'administration

// inclusion du fichier de connexion
require_once '../traitements/db.php'; // verifie que le chemin est correct et que $conn est bien l'objet pdo

// securite basique (a ameliorer pour une vraie application)
/*
session_start(); // demarre la session si necessaire
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // redirige vers la page de connexion
    header('Location: login.php'); // adapte le nom de la page de connexion
    exit;
}
*/
// pour cet exemple, on suppose que l'acces est autorise

// recuperation des donnees

// utilisateurs
try {
    // utilise $conn si c'est le nom de la variable pdo dans db.php
    $stmt_users = $conn->query("SELECT id, nom, prenom, email, admin, date_creation FROM utilisateurs ORDER BY nom, prenom");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC); // bonne pratique de specifier le fetch mode
} catch (\PDOException $e) {
    $error_users = "erreur lors de la recuperation des utilisateurs: " . $e->getMessage();
    $users = [];
}

// commandes avec nom de l'utilisateur
try {
    $stmt_orders = $conn->query("
        SELECT c.id, c.date_commande, c.prix_total, c.statut, u.nom AS user_nom, u.prenom AS user_prenom
        FROM commandes c
        JOIN utilisateurs u ON c.utilisateur_id = u.id
        ORDER BY c.date_commande DESC
    ");
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $error_orders = "erreur lors de la recuperation des commandes: " . $e->getMessage();
    $orders = [];
}

// stocks avec nom du produit et taille
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
    $error_stock = "erreur lors de la recuperation des stocks: " . $e->getMessage();
    $stock = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>panneau d'administration - miaou3</title>
    <style>
        /* styles pour le panneau d'administration */
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

        /* styles pour les tableaux */
        .scrollable-table-container {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #555;
            border-radius: 4px;
            margin-top: 15px;
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
            position: sticky;
            top: 0;
            z-index: 1;
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
        .actions .edit { background-color: #e67e22; }
        .actions .delete { background-color: #c0392b; }
        .actions .view { background-color: #2980b9; }
        .actions button.edit {
             background-color: #27ae60;
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
        .stock-form { display: flex; align-items: center; }
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
        td span.status-admin { color: #2ecc71; font-weight: bold; }
        td span.status-not-admin { color: #e74c3c; font-weight: bold; }

    </style>
</head>
<body>

    <h1>panneau d'administration miaou3</h1>

    <!-- section gestion des utilisateurs -->
    <section id="users">
        <h2>gestion des utilisateurs</h2>
        <?php if (isset($error_users)): ?>
            <p class="error"><?php echo htmlspecialchars($error_users); ?></p>
        <?php endif; ?>

        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>nom</th>
                        <th>prenom</th>
                        <th>email</th>
                        <th>admin</th>
                        <th>date creation</th>
                        <th>actions</th>
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
                                <span class="status-admin" title="administrateur">✔ oui</span>
                            <?php else: ?>
                                <span class="status-not-admin" title="utilisateur standard">✘ non</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['date_creation']))); ?></td>
                        <td class="actions">
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit" title="modifier l'utilisateur">modifier</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('etes-vous sur de vouloir supprimer cet utilisateur ?\nnom: <?php echo htmlspecialchars(addslashes($user['prenom'] . ' ' . $user['nom'])); ?>');" title="supprimer l'utilisateur">supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users) && !isset($error_users)): ?>
                    <tr><td colspan="7">aucun utilisateur trouve</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- section gestion des commandes -->
    <section id="orders">
        <h2>gestion des commandes</h2>
         <?php if (isset($error_orders)): ?>
            <p class="error"><?php echo htmlspecialchars($error_orders); ?></p>
        <?php endif; ?>

        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>n° commande</th>
                        <th>date</th>
                        <th>client</th>
                        <th>total</th>
                        <th>statut</th>
                        <th>actions</th>
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
                            <a href="view_order.php?id=<?php echo $order['id']; ?>" class="view" title="voir les details de la commande">details</a>
                            <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="edit" title="modifier le statut de la commande">statut</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders) && !isset($error_orders)): ?>
                    <tr><td colspan="6">aucune commande trouvee</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- section gestion des stocks -->
    <section id="stock">
        <h2>gestion des stocks</h2>
         <?php if (isset($error_stock)): ?>
            <p class="error"><?php echo htmlspecialchars($error_stock); ?></p>
        <?php endif; ?>

        <div class="scrollable-table-container">
            <table>
                <thead>
                    <tr>
                        <th>produit (id)</th>
                        <th>taille (id)</th>
                        <th>quantite</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['produit_nom']); ?> (<?php echo $item['produit_id'] ?>)</td>
                        <td><?php echo htmlspecialchars($item['taille_nom']); ?> (<?php echo $item['taille_id'] ?>)</td>
                        <td>
                             <form action="update_stock.php" method="POST" class="stock-form">
                                <input type="hidden" name="stock_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <input type="number" name="quantite" value="<?php echo htmlspecialchars($item['quantite']); ?>" min="0" required title="nouvelle quantite">
                                <button type="submit" class="actions edit" title="mettre a jour la quantite">màj</button>
                             </form>
                        </td>
                        <td class="actions">
                            <a href="edit_product.php?id=<?php echo $item['produit_id']; ?>" class="view" title="voir/modifier la fiche produit">produit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stock) && !isset($error_stock)): ?>
                    <tr><td colspan="4">aucun stock trouve</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</body>
</html>