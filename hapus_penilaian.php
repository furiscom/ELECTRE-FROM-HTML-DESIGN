<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: ". $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kodeAlternatif = $_POST["kode_alternatif"];

    // Hapus data penilaian
    $sql = "DELETE FROM penilaian WHERE kode_alternatif = '$kodeAlternatif'";

    if ($conn->query($sql) === TRUE) {
        echo "Data penilaian berhasil dihapus!";
    } else {
        echo "Error: ". $sql. "<br>". $conn->error;
    }
}

$conn->close();