<?php
$servername = "localhost"; // Ganti jika perlu (misal: nama host dari provider hosting)
$username = "root";      // Ganti dengan username database Anda
$password = "";          // Ganti dengan password database Anda
$dbname = "kasir";        // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengatur header untuk output JSON
header('Content-Type: application/json');
?>