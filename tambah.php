<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $kode_barang    = $_POST['kode_barang'] ?? '';
    $nama_barang    = $_POST['nama_barang'];
    $kategori       = $_POST['kategori'];
    $jumlah         = $_POST['jumlah'];
    $harga          = $_POST['harga'];
    $supplier       = $_POST['supplier'];
    $tanggal_masuk  = $_POST['tanggal_masuk'];

    if (empty($nama_barang)) {
        $errors[] = "Nama barang wajib diisi!";
    }

    if (!is_numeric($jumlah) || $jumlah < 0) {
        $errors[] = "Jumlah harus angka dan tidak boleh negatif!";
    }

    if (!is_numeric($harga) || $harga < 0) {
        $errors[] = "Harga harus angka dan tidak boleh negatif!";
    }

    if (empty($supplier)) {
        $errors[] = "Supplier wajib diisi!";
    }

    if (empty($kode_barang)) {
        $prefix = "BRG";

        $stmt = $koneksi->prepare("
            SELECT MAX(SUBSTRING(kode_barang,4)) as max_code
            FROM barang
            WHERE kode_barang LIKE ?
        ");
        $stmt->execute([$prefix . "%"]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = ($row['max_code'] ?? 0) + 1;
        $kode_barang = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    $namaFile = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {

        $fileTmp  = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format gambar harus JPG/JPEG/PNG!";
        }

        if ($fileSize > 2 * 1024 * 1024) {
            $errors[] = "Ukuran gambar maksimal 2MB!";
        }

        $mime = mime_content_type($fileTmp);
        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            $errors[] = "File harus berupa gambar valid!";
        }

        if (empty($errors)) {
            $namaFile = uniqid() . '.' . $ext;
            move_uploaded_file($fileTmp, "uploads/" . $namaFile);
        }
    }

    if (empty($errors)) {

        $query = $koneksi->prepare("
            INSERT INTO barang 
            (kode_barang, nama_barang, kategori, jumlah, harga, supplier, tanggal_masuk, gambar)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $query->execute([
            $kode_barang,
            $nama_barang,
            $kategori,
            $jumlah,
            $harga,
            $supplier,
            $tanggal_masuk,
            $namaFile
        ]);

        $_SESSION['pesan'] = "Barang berhasil ditambahkan";
        header("Location:index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #eef2ff, #fdf4ff);
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal {
            width: 650px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
        }

        .modal-header {
            background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
            color: white;
            padding: 22px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-body {
            padding: 30px 35px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
        }

        input {
            height: 45px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid #f0b3dd;
            background: #fde7f3;
            outline: none;
        }

        .full {
            grid-column: 1 / 3;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .batal {
            background: white;
            border: 1px solid #f0b3dd;
        }

        .simpan {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
        }

        input[type="file"] {
            height: auto;
            padding: 10px;
            background: #fde7f3;
            border: 1px solid #f0b3dd;
            border-radius: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        
        input[type="file"]::file-selector-button {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            margin-right: 10px;
            cursor: pointer;
        }

        .form-group input[type="file"] {
            min-height: 45px;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-plus"></i> Tambah Barang</h2>
        </div>

        <div class="modal-body">

            <?php if (!empty($errors)): ?>
                <div style="background:#ffe5e5; padding:10px; border-radius:8px; color:#b30000; margin-bottom:15px;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" name="kode_barang" placeholder="Kosongkan untuk otomatis"
                        value="<?= htmlspecialchars($_POST['kode_barang'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="kategori" required
                        value="<?= htmlspecialchars($_POST['kategori'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" required
                        value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" required
                        value="<?= htmlspecialchars($_POST['jumlah'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" required
                        value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="<?= $_POST['tanggal_masuk'] ?? date('Y-m-d') ?>">
                    </div>

                    <div class="form-group full">
                        <label>Gambar</label>
                        <input type="file" name="gambar">
                    </div>

                    <div class="form-group full">
                        <label>Supplier</label>
                        <input type="text" name="supplier" required
                        value="<?= htmlspecialchars($_POST['supplier'] ?? '') ?>">
                    </div>
                    </div>

                    <div class="actions">
                        <button type="button" class="btn batal" onclick="history.back()">Batal</button>
                        <button type="submit" class="btn simpan">Simpan</button>
                    </div>
            </form>
        </div>
    </div>

</body>
</html>