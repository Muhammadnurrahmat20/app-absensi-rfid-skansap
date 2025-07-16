<?php
// error_reporting(0);
date_default_timezone_set("Asia/Makassar");
$host     = "localhost";
$username = "root";
$password = "";
$database = "app-rfid-absensi-smea";

// Membuat $hostname
$koneksi = new mysqli($host, $username, $password, $database);

// Cek Koneksi
if ($koneksi->connect_error) {
    die("Koneksi Gagal : " . $koneksi->connect_error);
}
?>