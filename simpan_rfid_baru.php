<?php
require_once("./config/db.php");

// Set header untuk respon teks biasa
header('Content-Type: text/plain');

// Hanya proses jika ada data UID yang dikirim
if (isset($_POST["uid"])) {
    $uid = $_POST["uid"];

    // 1. Cek apakah UID sudah terdaftar di tabel pegawai
    $stmt_pegawai = $koneksi->prepare("SELECT pegawai_rfid FROM pegawai WHERE pegawai_rfid = ?");
    $stmt_pegawai->bind_param("s", $uid);
    $stmt_pegawai->execute();
    if ($stmt_pegawai->get_result()->num_rows > 0) {
        http_response_code(400);
        die("FAIL: Kartu sudah terdaftar sebagai pegawai.");
    }
    $stmt_pegawai->close();

    // 2. Cek apakah UID sudah ada di tabel rfid_code
    $stmt_check = $koneksi->prepare("SELECT rfid_code FROM rfid_code WHERE rfid_code = ?");
    $stmt_check->bind_param("s", $uid);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        http_response_code(400);
        die("FAIL: Kartu sudah ada di daftar, siap digunakan.");
    }
    $stmt_check->close();

    // 3. Jika belum ada, masukkan UID baru ke tabel rfid_code
    $stmt_insert = $koneksi->prepare("INSERT INTO rfid_code (rfid_code, used) VALUES (?, 0)");
    $stmt_insert->bind_param("s", $uid);

    if ($stmt_insert->execute()) {
        http_response_code(200);
        echo "OK"; // Kirim respon sukses
    } else {
        http_response_code(500);
        die("FAIL: Gagal simpan ke database.");
    }
    $stmt_insert->close();

} else {
    http_response_code(400);
    die("FAIL: Tidak ada data UID yang dikirim.");
}

$koneksi->close();
?>
