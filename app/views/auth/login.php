<?php

$error = $error ?? '';

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Login System</title>

<style>

* {

    margin: 0;
    padding: 0;

    box-sizing: border-box;

    font-family:
    'Segoe UI',
    Tahoma,
    Geneva,
    Verdana,
    sans-serif;
}

body {

    background:
    linear-gradient(
        135deg,
        #eef2ff,
        #fdf4ff
    );

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 20px;

    color: #2d3436;
}

.login-container {

    background: #FFFFFF;

    border-radius: 18px;

    box-shadow:
    0 18px 40px rgba(236, 72, 153, 0.2);

    width: 100%;

    max-width: 420px;

    overflow: hidden;
}

.login-header {

    background:
    linear-gradient(
        90deg,
        #6366f1,
        #a855f7,
        #ec4899
    );

    color: white;

    padding: 26px 22px;

    text-align: center;
}

.login-header h1 {

    font-size: 24px;

    margin-bottom: 6px;

    font-weight: 700;
}

.login-header p {

    opacity: 0.95;

    font-size: 14px;
}

.login-form {

    padding: 24px;
}

.form-group {

    margin-bottom: 18px;
}

.form-group label {

    display: block;

    margin-bottom: 8px;

    font-weight: 600;

    font-size: 14px;
}

.form-group input[type="text"],
.form-group input[type="password"] {

    width: 100%;

    padding: 12px 14px;

    border:
    1px solid #f1c0e8;

    border-radius: 10px;

    font-size: 14px;

    background: #fff0f6;
}

.form-group input:focus {

    border-color: #a855f7;

    outline: none;

    background: #fff;

    box-shadow:
    0 0 0 3px rgba(168, 85, 247, 0.25);
}

.error-message {

    background-color: #ffe4e6;

    color: #e11d48;

    padding: 10px 12px;

    border-radius: 10px;

    margin-bottom: 18px;

    font-size: 13px;
}

.btn-login {

    background:
    linear-gradient(
        90deg,
        #a855f7,
        #ec4899
    );

    color: white;

    border: none;

    padding: 12px;

    width: 100%;

    border-radius: 10px;

    font-size: 15px;

    font-weight: 700;

    cursor: pointer;

    box-shadow:
    0 6px 18px rgba(168, 85, 247, 0.45);

    transition: 0.2s;
}

.btn-login:hover {

    background:
    linear-gradient(
        90deg,
        #9333ea,
        #db2777
    );
}

.register-link {

    text-align: center;

    margin-top: 18px;

    font-size: 13px;
}

.register-link a {

    color: #a855f7;

    text-decoration: none;

    font-weight: 600;
}

.register-link a:hover {

    text-decoration: underline;
}

</style>

</head>

<body>

<div class="login-container">

    <div class="login-header">

        <h1>Selamat Datang</h1>

        <p>
            Silakan login untuk melanjutkan
        </p>

    </div>

    <form
        class="login-form"
        method="POST"
        action="index.php?action=login"
    >

        <?php if($error): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <div class="form-group">

            <label>Username</label>

            <input
                type="text"
                name="username"
                required
                placeholder="Masukkan username Anda"
            >

        </div>

        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
                placeholder="Masukkan password Anda"
            >

        </div>

        <div style="margin-bottom:15px;">

            <label>

                <input
                    type="checkbox"
                    name="remember"
                >

                Ingat saya

            </label>

        </div>

        <button
            type="submit"
            class="btn-login"
        >

            Login

        </button>

        <div class="register-link">

            Belum punya akun?

            <a href="index.php?action=register">

                Daftar di sini

            </a>

        </div>

    </form>

</div>

</body>
</html>