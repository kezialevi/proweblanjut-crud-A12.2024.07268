<?php
require 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Password tidak cocok!";
    } else {

        $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username sudah digunakan!";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $koneksi->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);

            header("Location: login.php?success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #eef2ff, #fdf4ff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #2d3436;
        }
        
        .register-container {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(236, 72, 153, 0.2);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
            color: white;
            padding: 26px 22px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 24px;
            margin-bottom: 6px;
            font-weight: 700;
        }
        
        .register-header p {
            opacity: 0.95;
            font-size: 14px;
        }
        
        .register-form {
            padding: 24px 24px 26px;
            background: #fff;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3436;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #f1c0e8;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.25s;
            background: #fff0f6;
        }
        
        .form-group input:focus {
            border-color: #a855f7;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.25);
        }
        
        .error-message {
            background-color: #ffe4e6;
            color: #e11d48;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            border-left: 4px solid #ec4899;
            font-size: 13px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }
        
        .btn-register {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 18px rgba(168, 85, 247, 0.45);
        }
        
        .btn-register:hover {
            background: linear-gradient(90deg, #9333ea, #db2777);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(168, 85, 247, 0.6);
        }
        
        .login-link {
            text-align: center;
            margin-top: 18px;
            color: #6B7280;
            font-size: 13px;
        }
        
        .login-link a {
            color: #a855f7;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Buat Akun</h1>
            <p>Silakan daftar untuk melanjutkan</p>
        </div>
        
        <form class="register-form" method="POST">
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text"
                       id="username"
                       name="username"
                       required
                       placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       placeholder="Masukkan password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       required
                       placeholder="Ulangi password">
            </div>
            
            <button type="submit" class="btn-register">Daftar</button>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </form>
    </div>
</body>
</html>