<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_POST['id_penilaian'])) {
    $id_penilaian = $_POST['id_penilaian'];

    // Query untuk menghapus data penilaian berdasarkan id_penilaian
    $sql = "DELETE FROM penilaian WHERE id_penilaian = $id_penilaian";

    if ($conn->query($sql) === TRUE) {
        echo "Data penilaian berhasil dihapus!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "ID Penilaian tidak ditemukan.";
}

$conn->close();
?>