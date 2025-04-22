<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un nouvel item</title>
</head>
<body>

  <h1>Ajouter un nouvel item au shop</h1>

  <form action="../traitements/traitement_ajouter_item.php" method="POST">
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


            <style>
                .success-message {
                    color: green;
                    background-color: #f8d7da;
                    border: 1px solid #f5c6cb;
                    padding: 10px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                    text-align: center;
                }
            </style>


</body>
</html>
