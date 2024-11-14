<?php
// logout.php

require_once 'config.php';  // A config fájl betöltése

session_destroy();
setcookie('remember_token', '', time() - 3600, '/');  // Kijelentkezéskor eltávolítjuk a 'remember_token' cookie-t
header("Location: login.php");  // Visszairányít a bejelentkezéshez
exit();
?>
