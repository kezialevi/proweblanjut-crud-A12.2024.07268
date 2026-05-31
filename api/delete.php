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

if ($model->delete($id)) {

    echo json_encode([
        "status" => true,
        "message" => "Barang berhasil dihapus"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Gagal menghapus barang"
    ]);
}