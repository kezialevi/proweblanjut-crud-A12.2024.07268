<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $koneksi->prepare("DELETE FROM barang WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>