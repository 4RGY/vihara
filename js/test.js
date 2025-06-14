<?php
// Konfigurasi database
$servername = "localhost"; //tetep begini,default
$username = "root"; //tetep begini,default
$password = ""; //tetep begini,default
$dbname = "nama_database"; //ini diganti jadi nama database piti

//buat variable koneksi yang pake function mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// cek kondisi koneksi berhasil apa gagal
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil!";

// Tutup koneksi
$conn->close();
?>