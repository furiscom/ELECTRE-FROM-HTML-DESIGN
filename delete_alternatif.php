<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_POST['kode_alternatif'])) {
    $kode_alternatif = $_POST['kode_alternatif'];

    // Query untuk menghapus data alternatif berdasarkan kode_alternatif
    $sql = "DELETE FROM alternatif WHERE kode_alternatif = '$kode_alternatif'";

    if ($conn->query($sql) === TRUE) {
        echo "Data alternatif berhasil dihapus!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Kode alternatif tidak ditemukan.";
}

$conn->close();
?>