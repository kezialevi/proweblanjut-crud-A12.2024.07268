<?php
include 'koneksi.php';

$success = '';
$error   = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username         = $_POST['username'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap     = $_POST['nama_lengkap'];
    $email            = $_POST['email'];

    if ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {

        // CEK USERNAME (PDO)
        $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username sudah digunakan!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $koneksi->prepare("
                INSERT INTO users (username, password, nama_lengkap, email)
                VALUES (?, ?, ?, ?)
            ");

            if ($stmt->execute([$username, $hashed_password, $nama_lengkap, $email])) {
                $success = "Pendaftaran berhasil!";
            } else {
                $error = "Gagal daftar!";
            }
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
            max-width: 460px;
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
            font-size: 14px;
            opacity: 0.95;
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
            margin-bottom: 6px;
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
        
        .success-message {
            background-color: #fce7f3;
            color: #be185d;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            border-left: 4px solid #ec4899;
            font-size: 13px;
            display: <?php echo $success ? 'block' : 'none'; ?>;
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
            margin-top: 6px;
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
            <h1>Buat Akun Baru</h1>
            <p>Daftar untuk mengakses sistem</p>
        </div>
        
        <form class="register-form" method="POST" action="">
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required 
                       placeholder="Masukkan nama lengkap Anda">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="Masukkan email Anda">
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Buat username unik">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Buat password (min. 6 karakter)">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Ulangi password Anda">
            </div>
            
            <button type="submit" class="btn-register">Daftar Sekarang</button>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </form>
    </div>
</body>
</html>