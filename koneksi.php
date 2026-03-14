<?php

$host = "localhost";
$db   = "inventaris_db";
$user = "root";
$pass = "";

try {

    $koneksi = new PDO("mysql:host=$host;dbname=$db;charset=utf8",$user,$pass);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("Koneksi gagal : ".$e->getMessage());

}

?>