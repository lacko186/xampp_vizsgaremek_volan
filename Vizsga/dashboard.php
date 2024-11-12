<?php
// dashboard.php

require_once 'config.php';  // A config fájl betöltése

// Ellenőrizni kell, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Ha nincs bejelentkezve, átirányítjuk a login oldalra
    exit();
}

echo "Üdvözöljük, " . $_SESSION['username'] . "!";  // A felhasználó nevét jelenítjük meg
?>
