<?php
require 'koneksi.php';

if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];

    $sql = "SELECT * FROM barang 
            WHERE nama_barang LIKE :search 
            OR kode_barang LIKE :search 
            OR kategori LIKE :search 
            ORDER BY id DESC";

    $stmt = $koneksi->prepare($sql);
    $stmt->execute(['search' => "%$keyword%"]);
    $data_barang = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $no = 1;

    if (count($data_barang) > 0) {
        foreach ($data_barang as $barang) {
?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <span class="kode">
                        #<?= htmlspecialchars($barang['kode_barang']) ?>
                    </span>
                </td>
                <td>
                    <strong><?= htmlspecialchars($barang['nama_barang']) ?></strong>
                </td>
                <td><?= htmlspecialchars($barang['kategori']) ?></td>
                <td>
                    <span class="badge">
                        <?= $barang['jumlah'] ?> Unit
                    </span>
                </td>
                <td>
                    <span class="harga">
                        Rp <?= number_format($barang['harga'], 0, ',', '.') ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($barang['supplier']) ?></td>
                <td><?= $barang['tanggal_masuk'] ?></td>
                <td style="text-align:center">
                    <a href="edit.php?id=<?= $barang['id'] ?>" class="btn edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <a href="hapus.php?id=<?= $barang['id'] ?>" class="btn delete" onclick="return confirm('Yakin hapus data?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
<?php
        }
    } else {
?>
        <tr>
            <td colspan="9" style="text-align:center;padding:40px">
                Data tidak ditemukan
            </td>
        </tr>
<?php
    }
    exit;
}

$sql = "SELECT * FROM barang ORDER BY id DESC";
$stmt = $koneksi->query($sql);
$data_barang = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #a855f7;
            --secondary: #ec4899;
            --bg: #fdf4ff;
            --white: #fff;
            --text: #2d3436;
            --accent: #c084fc;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            margin: 0;
            color: var(--text);
        }

        .navbar {
            background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
            padding: 15px 40px;
            color: white;
            display: flex;
            justify-content: space-between; /* ✅ supaya kiri-kanan */
            align-items: center;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            font-size: 18px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.35);
        }

        .container {
            max-width: 1300px;
            margin: auto;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
        }

        .tools {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-box input {
            padding: 10px 15px;
            border-radius: 10px;
            border: 1px solid #f1c0e8;
            background: #fff0f6;
            width: 220px;
            outline: none;
        }

        .btn-tambah {
            background: linear-gradient(90deg, #a855f7, #ec4899);
            color: white;
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
            color: white;
        }

        th, td {
            padding: 14px;
            text-align: left;
            font-size: 14px;
        }

        td {
            border-bottom: 1px solid #f8d7ff;
        }

        .kode {
            color: #9333ea;
            font-weight: 600;
        }

        .badge {
            background: #fce7f3;
            color: #be185d;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .harga {
            color: #a21caf;
            font-weight: 700;
        }

        .btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            text-decoration: none;
            margin: 0 2px;
        }

        .edit {
            background: #fdf2f8;
            color: #db2777;
        }

        .delete {
            background: #ffe4e6;
            color: #e11d48;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="nav-left">
            <i class="fas fa-box-open"></i> Sistem Inventaris
        </div>

        <!-- ✅ tombol logout -->
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="title">Daftar Barang</div>
                <div class="tools">
                    <div class="search-box">
                        <input type="text" id="search" placeholder="Cari barang...">
                    </div>
                    <a href="tambah.php" class="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Supplier</th>
                        <th>Tanggal Masuk</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-data">
                    <?php $no = 1; foreach ($data_barang as $barang): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="kode">#<?= htmlspecialchars($barang['kode_barang']) ?></span></td>
                            <td><strong><?= htmlspecialchars($barang['nama_barang']) ?></strong></td>
                            <td><?= htmlspecialchars($barang['kategori']) ?></td>
                            <td><span class="badge"><?= $barang['jumlah'] ?> Unit</span></td>
                            <td><span class="harga">Rp <?= number_format($barang['harga'], 0, ',', '.') ?></span></td>
                            <td><?= htmlspecialchars($barang['supplier']) ?></td>
                            <td><?= $barang['tanggal_masuk'] ?></td>
                            <td style="text-align:center">
                                <a href="edit.php?id=<?= $barang['id'] ?>" class="btn edit"><i class="fas fa-pen"></i></a>
                                <a href="hapus.php?id=<?= $barang['id'] ?>" class="btn delete" onclick="return confirm('Yakin hapus data?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("search").addEventListener("keyup", function() {
            let keyword = this.value;
            fetch("?keyword=" + keyword)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("table-data").innerHTML = data;
                });
        });
    </script>

</body>
</html>