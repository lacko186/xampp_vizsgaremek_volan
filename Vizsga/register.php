<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Ellenőrzések
    $errors = [];
    
    // Felhasználónév ellenőrzése
    if (empty($username)) {
        $errors[] = "A felhasználónév megadása kötelező!";
    }
    
    // Email ellenőrzése
    if (empty($email)) {
        $errors[] = "Az email cím megadása kötelező!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Érvénytelen email cím!";
    }
    
    // Jelszó ellenőrzése
    if (empty($password)) {
        $errors[] = "A jelszó megadása kötelező!";
    } elseif (strlen($password) < 6) {
        $errors[] = "A jelszónak legalább 6 karakter hosszúnak kell lennie!";
    }
    
    // Jelszó egyezés ellenőrzése
    if ($password !== $password_confirm) {
        $errors[] = "A jelszavak nem egyeznek!";
    }
    
    // Felhasználónév és email egyediségének ellenőrzése
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "A felhasználónév vagy email cím már foglalt!";
    }
    
    // Ha nincs hiba, akkor regisztráljuk a felhasználót
    if (empty($errors)) {
        // Jelszó hashelése
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Felhasználó beszúrása az adatbázisba
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Sikeres regisztráció! Most már bejelentkezhet.";
            header("Location: index.html");
            exit();
        } else {
            $errors[] = "Hiba történt a regisztráció során. Kérjük, próbálja újra!";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: index.html");
        exit();
    }
}
?>