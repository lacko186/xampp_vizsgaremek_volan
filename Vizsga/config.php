<?php

$db_host = 'localhost';
$db_user = 'root';       
$db_pass = '';          
$db_name = 'volan_app';  

try {
    // PDO kapcsolat létrehozása
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    
    // Hibakezelés beállítása
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Hibakezelés: hibaüzenet naplózása, amit a felhasználónak nem kell látnia
    error_log("Kapcsolódási hiba: " . $e->getMessage());
    die("Kapcsolódási hiba! Kérjük próbálja meg később.");
}
?>
