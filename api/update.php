<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../app/models/BarangModel.php";

$model = new BarangModel();

$id = $_POST['id'] ?? null;

if (!$id) {

    echo json_encode([
        "status" => false,
        "message" => "ID wajib diisi"
    ]);

    exit;
}

$data = [

    'kode_barang'   => $_POST['kode_barang'],
    'nama_barang'   => $_POST['nama_barang'],
    'kategori'      => $_POST['kategori'],
    'jumlah'        => $_POST['jumlah'],
    'harga'         => $_POST['harga'],
    'supplier'      => $_POST['supplier']??'',
    'tanggal_masuk' => $_POST['tanggal_masuk'],
    'gambar'        => null

];

if ($model->update($id, $data)) {

    echo json_encode([
        "status" => true,
        "message" => "Barang berhasil diupdate"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Gagal update barang"
    ]);
}