<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../app/models/BarangModel.php";

$model = new BarangModel();

$data = $model->getAll();

echo json_encode(
    $data,
    JSON_PRETTY_PRINT
);