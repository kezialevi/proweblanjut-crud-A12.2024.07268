<?php
require 'koneksi.php';
session_start();

$id = $_GET['id'];

$stmt = $koneksi->prepare("SELECT * FROM barang WHERE id=?");
$stmt->execute([$id]);
$barang = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$barang) {
    header("Location:index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $kode_barang    = $_POST['kode_barang'];
    $nama_barang    = $_POST['nama_barang'];
    $kategori       = $_POST['kategori'];
    $jumlah         = $_POST['jumlah'];
    $harga          = $_POST['harga'];
    $supplier       = $_POST['supplier'];
    $tanggal_masuk  = $_POST['tanggal_masuk'];

    $update = $koneksi->prepare("
        UPDATE barang SET
            kode_barang=?,
            nama_barang=?,
            kategori=?,
            jumlah=?,
            harga=?,
            supplier=?,
            tanggal_masuk=?
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
        $id
    ]);

    $_SESSION['pesan'] = "Data berhasil diupdate";
    header("Location:index.php");
    exit();
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

        /* MODAL */
        .modal {
            width: 650px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
        }

        /* HEADER */
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

        /* BODY */
        .modal-body {
            padding: 30px 35px;
        }

        /* GRID FORM */
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
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #a855f7;
            background: white;
        }

        input[readonly] {
            background: #f1f1f1;
            border-color: #ddd;
            cursor: not-allowed;
        }

        /* FULL WIDTH */
        .full {
            grid-column: 1 / 3;
        }

        /* BUTTON */
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

        .update {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
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
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" name="kode_barang" value="<?= htmlspecialchars($barang['kode_barang']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="kategori" value="<?= htmlspecialchars($barang['kategori']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" value="<?= $barang['jumlah'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" value="<?= $barang['harga'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="<?= $barang['tanggal_masuk'] ?>" required>
                    </div>

                    <div class="form-group full">
                        <label>Supplier</label>
                        <input type="text" name="supplier" value="<?= htmlspecialchars($barang['supplier']) ?>" required>
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