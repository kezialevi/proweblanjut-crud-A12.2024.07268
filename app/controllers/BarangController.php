<?php

require_once "../app/core/Database.php";
require_once "../app/models/BarangModel.php";

class BarangController {

    private $barangModel;

    public function __construct()
    {
        session_start();

        $this->barangModel = new BarangModel();
    }

    public function register()
    {
        if (isset($_SESSION['login'])) {

            header("Location: index.php");

            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $username = trim($_POST['username']);

            $password = $_POST['password'];

            $confirm =
            $_POST['confirm_password'];

            // VALIDASI
            if (empty($username)) {

                $error =
                "Username wajib diisi!";

            }

            elseif (strlen($username) < 3) {

                $error =
                "Username minimal 3 karakter!";

            }

            elseif ($password !== $confirm) {

                $error =
                "Password tidak cocok!";

            }

            elseif (strlen($password) < 6) {

                $error =
                "Password minimal 6 karakter!";

            }

            else {

                $database = new Database();

                $db = $database->conn;

                // CEK USERNAME
                $stmt = $db->prepare("
                    SELECT id
                    FROM users
                    WHERE username = ?
                ");

                $stmt->execute([$username]);

                if ($stmt->rowCount() > 0) {

                    $error =
                    "Username sudah digunakan!";

                }

                else {

                    // HASH PASSWORD
                    $hash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    // INSERT USER
                    $stmt = $db->prepare("
                        INSERT INTO users
                        (username, password)
                        VALUES (?, ?)
                    ");

                    $stmt->execute([
                        $username,
                        $hash
                    ]);

                    header(
                        "Location:index.php?action=login"
                    );

                    exit();
                }
            }
        }

        require "../app/views/auth/register.php";
    }

    // ================= LOGIN =================
    public function login()
    {
        // SUDAH LOGIN
        if (isset($_SESSION['login'])) {

            header("Location: index.php");

            exit();
        }

        // CEK COOKIE
        if (
            isset($_COOKIE['login'])
            &&
            isset($_COOKIE['username'])
        ) {

            $_SESSION['login'] = true;

            $_SESSION['username'] =
            $_COOKIE['username'];

            header("Location: index.php");

            exit();
        }

        $error = '';

        // PROSES LOGIN
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $username =
            trim($_POST['username']);

            $password =
            $_POST['password'];

            $remember =
            isset($_POST['remember']);

            $database = new Database();

            $db = $database->conn;

            $stmt = $db->prepare("
                SELECT * FROM users
                WHERE username = ?
            ");

            $stmt->execute([$username]);

            $user =
            $stmt->fetch(PDO::FETCH_ASSOC);

            // USER DITEMUKAN
            if ($user) {

                $login = false;

                // PASSWORD HASH
                if (
                    password_verify(
                        $password,
                        $user['password']
                    )
                ) {

                    $login = true;
                }

                // PASSWORD LAMA
                elseif (
                    $password ==
                    $user['password']
                ) {

                    $login = true;

                    $newHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $update = $db->prepare("
                        UPDATE users
                        SET password=?
                        WHERE id=?
                    ");

                    $update->execute([
                        $newHash,
                        $user['id']
                    ]);
                }

                // LOGIN BERHASIL
                if ($login) {

                    $_SESSION['login'] = true;

                    $_SESSION['user_id'] =
                    $user['id'];

                    $_SESSION['username'] =
                    $user['username'];

                    $_SESSION['nama_lengkap'] =
                    $user['nama_lengkap'];

                    // REMEMBER ME
                    if ($remember) {

                        setcookie(
                            "login",
                            "true",
                            time() + (60 * 60 * 24 * 7),
                            "/"
                        );

                        setcookie(
                            "username",
                            $user['username'],
                            time() + (60 * 60 * 24 * 7),
                            "/"
                        );

                    } else {

                        setcookie(
                            "login",
                            "true",
                            time() + 3600,
                            "/"
                        );

                        setcookie(
                            "username",
                            $user['username'],
                            time() + 3600,
                            "/"
                        );
                    }

                    header("Location: index.php");

                    exit();

                } else {

                    $error = "Password salah!";
                }

            } else {

                $error = "Username tidak ditemukan!";
            }
        }

        require "../app/views/auth/login.php";
    }

    // ================= LOGOUT =================
    public function logout()
    {
        session_unset();

        session_destroy();

        setcookie(
            "login",
            "",
            time() - 3600,
            "/"
        );

        setcookie(
            "username",
            "",
            time() - 3600,
            "/"
        );

        require "../app/views/auth/logout.php";
    }

    // ================= INDEX =================
    public function index()
    {
        // BELUM LOGIN
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        // SEARCH
        if (isset($_GET['keyword'])) {

            $keyword = $_GET['keyword'];

            $barang =
            $this->barangModel->search($keyword);

        } else {

            $barang =
            $this->barangModel->getAll();
        }

        require "../app/views/barang/index.php";
    }

    // ================= CREATE =================
    public function create()
    {
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        require "../app/views/barang/tambah.php";
    }

    // ================= STORE =================
    public function store()
    {
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        $data = $_POST;

        // AUTO KODE BARANG
        if (empty($data['kode_barang'])) {

            $data['kode_barang'] =
            $this->barangModel->generateKodeBarang();
        }

        // UPLOAD GAMBAR
        $data['gambar'] =
        $this->uploadGambar();

        // SIMPAN
        $this->barangModel->save($data);

        header("Location: index.php");
    }

    // ================= EDIT =================
    public function edit()
    {
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        $id = $_GET['id'];

        $barang =
        $this->barangModel->getById($id);

        require "../app/views/barang/edit.php";
    }

    // ================= UPDATE =================
    public function update()
    {
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        $id = $_GET['id'];

        $barangLama =
        $this->barangModel->getById($id);

        $data = $_POST;

        // DEFAULT GAMBAR LAMA
        $data['gambar'] =
        $barangLama['gambar'];

        // JIKA ADA GAMBAR BARU
        if (
            isset($_FILES['gambar'])
            &&
            $_FILES['gambar']['error'] == 0
        ) {

            // HAPUS GAMBAR LAMA
            if (
                $barangLama['gambar']
                &&
                file_exists(
                    "uploads/" .
                    $barangLama['gambar']
                )
            ) {

                unlink(
                    "uploads/" .
                    $barangLama['gambar']
                );
            }

            // UPLOAD BARU
            $data['gambar'] =
            $this->uploadGambar();
        }

        // UPDATE DB
        $this->barangModel->update(
            $id,
            $data
        );

        header("Location: index.php");
    }

    // ================= DELETE =================
    public function destroy()
    {
        if (!isset($_SESSION['login'])) {

            header("Location: index.php?action=login");

            exit();
        }

        $id = $_GET['id'];

        $barang =
        $this->barangModel->getById($id);

        // HAPUS FILE
        if (
            $barang['gambar']
            &&
            file_exists(
                "uploads/" .
                $barang['gambar']
            )
        ) {

            unlink(
                "uploads/" .
                $barang['gambar']
            );
        }

        // HAPUS DATA
        $this->barangModel->delete($id);

        header("Location: index.php");
    }

    // ================= UPLOAD GAMBAR =================
    private function uploadGambar()
    {
        if (
            isset($_FILES['gambar'])
            &&
            $_FILES['gambar']['error'] == 0
        ) {

            $tmp =
            $_FILES['gambar']['tmp_name'];

            $ext = strtolower(
                pathinfo(
                    $_FILES['gambar']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $namaFile =
            uniqid() . '.' . $ext;

            move_uploaded_file(
                $tmp,
                "uploads/" . $namaFile
            );

            return $namaFile;
        }

        return null;
    }
}