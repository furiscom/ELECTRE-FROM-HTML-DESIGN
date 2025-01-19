<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_GET['id_penilaian'])) {
    $id_penilaian = $_GET['id_penilaian'];

    // Query untuk mengambil data penilaian berdasarkan id_penilaian
    $sql = "SELECT * FROM penilaian WHERE id_penilaian = $id_penilaian";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo "Penilaian tidak ditemukan.";
    }
} else {
    echo "ID Penilaian tidak ditemukan.";
}

$conn->close();
?>