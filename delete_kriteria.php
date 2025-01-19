<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_POST['kode_kriteria'])) {
    $kode_kriteria = $_POST['kode_kriteria'];

    // Query untuk menghapus data kriteria berdasarkan kode_kriteria
    $sql = "DELETE FROM kriteria WHERE kode_kriteria = '$kode_kriteria'";

    if ($conn->query($sql) === TRUE) {
        echo "Data kriteria berhasil dihapus!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Kode kriteria tidak ditemukan.";
}

$conn->close();
?>