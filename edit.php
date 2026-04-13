<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location:index.php");
    exit();
}

$stmt = $koneksi->prepare("SELECT * FROM barang WHERE id=?");
$stmt->execute([$id]);
$barang = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$barang) {
    header("Location:index.php");
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

    // ================= VALIDASI =================
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

    // ================= UPLOAD GAMBAR =================
$namaFile = $barang['gambar']; // default gambar lama

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

        // 🔥 HAPUS GAMBAR LAMA
        if ($barang['gambar'] && file_exists("uploads/" . $barang['gambar'])) {
            unlink("uploads/" . $barang['gambar']);
        }

        $namaFile = uniqid() . '.' . $ext;
        move_uploaded_file($fileTmp, "uploads/" . $namaFile);
    }
}

    // ================= UPDATE =================
    if (empty($errors)) {

        $update = $koneksi->prepare("
            UPDATE barang SET
                kode_barang=?,
                nama_barang=?,
                kategori=?,
                jumlah=?,
                harga=?,
                supplier=?,
                tanggal_masuk=?,
                gambar=?
            WHERE id=?
        ");

        $update->execute([
            $kode_barang,
            $nama_barang,
            $kategori,
            $jumlah,
            $harga,
            $supplier,
            $tanggal_masuk,
            $namaFile,
            $id
        ]);

        $_SESSION['pesan'] = "Data berhasil diupdate";
        header("Location:index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
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
            display: flex;
            gap: 10px;
            align-items: center;
            font-size: 1.5rem;
        }

        .close {
            background: rgba(255, 255, 255, .25);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
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
            font-size: 14px;
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
        }

        .batal {
            background: white;
            border: 1px solid #f0b3dd;
        }

        .update {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
        }

        /* FILE INPUT BIAR RAPI */
        input[type="file"] {
            height: auto;
            padding: 10px;
            width: 100%;
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
        <h2><i class="fas fa-pen"></i> Edit Barang</h2>
        <button class="close" onclick="history.back()">&times;</button>
    </div>

    <div class="modal-body">

        <!-- ERROR -->
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
                    <input type="text" name="kode_barang"
                    value="<?= htmlspecialchars($_POST['kode_barang'] ?? $barang['kode_barang']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="kategori"
                    value="<?= htmlspecialchars($_POST['kategori'] ?? $barang['kategori']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang"
                    value="<?= htmlspecialchars($_POST['nama_barang'] ?? $barang['nama_barang']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah"
                    value="<?= htmlspecialchars($_POST['jumlah'] ?? $barang['jumlah']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga"
                    value="<?= htmlspecialchars($_POST['harga'] ?? $barang['harga']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk"
                    value="<?= $_POST['tanggal_masuk'] ?? $barang['tanggal_masuk'] ?>" required>
                </div>

                <div class="form-group full">
                    <label>Gambar</label>
                    <input type="file" name="gambar">
                </div>

                <div class="form-group full">
                    <label>Supplier</label>
                    <input type="text" name="supplier"
                    value="<?= htmlspecialchars($_POST['supplier'] ?? $barang['supplier']) ?>" required>
                </div>

            </div>

            <div class="actions">
                <button type="button" class="btn batal" onclick="history.back()">Batal</button>
                <button type="submit" class="btn update">Update</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>