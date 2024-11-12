<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $error . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
    unset($_SESSION['errors']);
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Volánbusz</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --volan-blue: #004b93;
            --volan-yellow: #ffd800;
        }
        
        body {
            background: linear-gradient(135deg, #00008b 0%, #323232 100%);
            height: 100vh;
        }
        
        .register-container {
            max-width: 450px;
            padding: 2.5rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .icon-container {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-in;
            position: relative;
        }
        
        .icon-container i {
            font-size: 3.5rem;
            color: var(--volan-blue);
            background: linear-gradient(45deg, var(--volan-blue), #0066cc);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .icon-container::after {
            content: '+';
            position: absolute;
            font-size: 2rem;
            color: var(--volan-yellow);
            font-weight: bold;
            right: 35%;
            top: -5px;
        }
        
        .register-title {
            color: var(--volan-blue);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .btn-volan {
            background: linear-gradient(45deg, orange, #FF4500);
            color: white;
            padding: 0.8rem;
            border: none;
            transition: all 0.3s;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-volan:hover {
	    backgroud-color: yellow;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,75,147,0.2);
            color: white;
        }
        
        .form-control {
            border-left: none;
            padding: 0.8rem;
            border-radius: 0 8px 8px 0;
        }
        
        .form-control:focus {
            border-color: var(--volan-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,75,147,0.15);
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 0.8rem;
        }
        
        .login-link {
            color: var(--volan-blue);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .login-link:hover {
            color: #0066cc;
            text-decoration: none;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-group:focus-within {
            box-shadow: 0 0 0 0.2rem rgba(0,75,147,0.15);
            border-radius: 8px;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="register-container">
            <div class="icon-container">
                <i class="fas fa-bus"></i>
            </div>
            
            <h2 class="text-center register-title">Regisztráció</h2>
            
            <form action="index.php" method="POST">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user text-muted"></i>
                        </span>
                        <input type="text" class="form-control" id="registerUsername" 
                               name="registerUsername" placeholder="Felhasználónév" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                        <input type="email" class="form-control" id="registerEmail" 
                               name="registerEmail" placeholder="Email cím" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock text-muted"></i>
                        </span>
                        <input type="password" class="form-control" id="registerPassword" 
                               name="registerPassword" placeholder="Jelszó" required>
                    </div>
                    <div class="password-requirements">
                        <i class="fas fa-info-circle me-1"></i>A jelszónak minimum 8 karaktert kell tartalmaznia
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock text-muted"></i>
                        </span>
                        <input type="password" class="form-control" id="registerPasswordConfirm" 
                               name="registerPasswordConfirm" placeholder="Jelszó megerősítése" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="register" class="btn btn-volan">
                        <i class="fas fa-user-plus me-2"></i>Regisztráció
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="mb-0">Már van fiókod? 
                    <a href="login.php" class="login-link">
                        <i class="fas fa-sign-in-alt me-1"></i>Bejelentkezés
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>