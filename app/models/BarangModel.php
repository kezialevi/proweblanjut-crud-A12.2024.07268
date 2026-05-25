<?php

require_once "../app/core/Database.php";

class BarangModel {

    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->conn;
    }

    // ================= GET ALL =================
    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT * FROM barang ORDER BY id DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================= SEARCH =================
    public function search($keyword)
    {
        $sql = "SELECT * FROM barang
                WHERE nama_barang LIKE :search
                OR kode_barang LIKE :search
                OR kategori LIKE :search
                ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'search' => "%$keyword%"
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================= GET BY ID =================
    public function getById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM barang WHERE id=?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ================= GENERATE KODE =================
    public function generateKodeBarang()
    {
        $prefix = "BRG";

        $stmt = $this->db->prepare("
            SELECT MAX(SUBSTRING(kode_barang,4))
            as max_code
            FROM barang
            WHERE kode_barang LIKE ?
        ");

        $stmt->execute([$prefix . "%"]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $next = ($row['max_code'] ?? 0) + 1;

        return $prefix .
        str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // ================= SAVE =================
    public function save($data)
    {
        $query = $this->db->prepare("
            INSERT INTO barang
            (
                kode_barang,
                nama_barang,
                kategori,
                jumlah,
                harga,
                supplier,
                tanggal_masuk,
                gambar
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $query->execute([
            $data['kode_barang'],
            $data['nama_barang'],
            $data['kategori'],
            $data['jumlah'],
            $data['harga'],
            $data['supplier'],
            $data['tanggal_masuk'],
            $data['gambar']
        ]);
    }

    // ================= UPDATE =================
    public function update($id, $data)
    {
        $query = $this->db->prepare("
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

        return $query->execute([
            $data['kode_barang'],
            $data['nama_barang'],
            $data['kategori'],
            $data['jumlah'],
            $data['harga'],
            $data['supplier'],
            $data['tanggal_masuk'],
            $data['gambar'],
            $id
        ]);
    }

    // ================= DELETE =================
    public function delete($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM barang WHERE id=?"
        );

        return $stmt->execute([$id]);
    }
}