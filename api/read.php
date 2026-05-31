<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../app/models/BarangModel.php";

$model = new BarangModel();

$data = [
    'kode_barang'   => $_POST['kode_barang'] ?? '',
    'nama_barang'   => $_POST['nama_barang'] ?? '',
    'kategori'      => $_POST['kategori'] ?? '',
    'jumlah'        => $_POST['jumlah'] ?? '',
    'harga'         => $_POST['harga'] ?? '',
    'supplier'      => $_POST['supplier'] ?? '',
    'tanggal_masuk' => $_POST['tanggal_masuk'] ?? '',
    'gambar'        => null
];

if (empty($data['kode_barang'])) {
    $data['kode_barang'] =
    $model->generateKodeBarang();
}

$result = $model->save($data);

if ($result) {
    echo json_encode([
        "status" => true,
        "message" => "Barang berhasil ditambahkan"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Barang gagal ditambahkan"
    ]);
}