<?php

session_start();

if (isset($_SESSION['id_user'])) {
    $userId = $_SESSION['id_user'];
    echo "User ID: " . htmlspecialchars($userId);
} else {
    echo "No user is currently logged in.";
}

?>