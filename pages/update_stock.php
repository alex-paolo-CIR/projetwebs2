<?php
session_start();
require_once '../traitements/db.php'; // Adjust path if needed

// Security: Check if user is admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    // Redirect non-admins or return an error (depending on how you want to handle it)
    // For an AJAX request you might return JSON, for direct form post, redirect.
    header('Location: accueil.php?error=unauthorized');
    exit;
}

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Input Validation ---
    $stock_id = filter_input(INPUT_POST, 'stock_id', FILTER_VALIDATE_INT);
    // Allow quantity 0, but not negative
    $quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

    // Check if inputs are valid integers (quantite can be 0, so check !== false)
    if ($stock_id === false || $stock_id <= 0 || $quantite === false) {
        // Invalid input
        header('Location: admin.php?error=invalid_input#stock');
        exit;
    }

    // --- Database Update ---
    try {
        $conn->beginTransaction(); // Start transaction

        // Prepare the update statement
        $sql = "UPDATE stock_produits SET quantite = :quantite WHERE id = :stock_id";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
        $stmt->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);

        // Execute the update
        $success = $stmt->execute();

        if ($success) {
            $conn->commit(); // Commit transaction
            // Redirect back to admin page with success message
            header('Location: admin.php?status=stock_updated#stock');
            exit;
        } else {
            $conn->rollBack(); // Rollback on failure
            // Redirect back with error message
            header('Location: admin.php?error=update_failed#stock');
            exit;
        }

    } catch (\PDOException $e) {
        $conn->rollBack(); // Rollback on exception
        // Log the error for debugging (don't show specific SQL errors to users)
        error_log("Stock Update Error: " . $e->getMessage());
        // Redirect back with a generic error message
        header('Location: admin.php?error=db_error#stock');
        exit;
    } finally {
        // Close connection if necessary (PDO usually handles this, but good practice if needed)
        // $conn = null;
    }

} else {
    // If accessed directly via GET or other method, redirect away
    header('Location: admin.php#stock');
    exit;
}
?>