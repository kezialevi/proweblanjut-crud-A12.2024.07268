<?php
session_start();

// HAPUS SESSION
session_unset();
session_destroy();

// HAPUS COOKIE
setcookie("login", "", time() - 3600);
setcookie("username", "", time() - 3600);
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="3;url=login.php">
<title>Logout</title>

<style>
body {
    margin: 0;
    height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #eef2ff, #fdf4ff);
    display: flex;
    justify-content: center;
    align-items: center;
}

.card {
    background: white;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(168, 85, 247, 0.25);
}

.loader {
    margin: 20px auto;
    width: 40px;
    height: 40px;
    border: 4px solid #f1c0e8;
    border-top: 4px solid #a855f7;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}

a {
    color: #a855f7;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="card">
    <h2>Anda telah logout</h2>
    <p>Mengalihkan ke halaman login...</p>

    <div class="loader"></div>

    <p>Jika tidak otomatis, <a href="login.php">klik di sini</a></p>
</div>

</body>
</html>