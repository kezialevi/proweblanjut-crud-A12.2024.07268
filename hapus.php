<?php
include 'koneksi.php';

$id = $_GET['id'];

$stmt = $koneksi->prepare("DELETE FROM barang WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>