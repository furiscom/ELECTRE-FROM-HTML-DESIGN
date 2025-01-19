<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk menghapus data pengguna berdasarkan id
    $sql = "DELETE FROM pengguna WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Data pengguna berhasil dihapus!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "ID tidak ditemukan.";
}

$conn->close();
?>