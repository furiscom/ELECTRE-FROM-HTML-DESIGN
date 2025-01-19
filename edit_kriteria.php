<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_GET['kode_kriteria'])) {
    $kode_kriteria = $_GET['kode_kriteria'];

    // Query untuk mengambil data kriteria berdasarkan kode_kriteria
    $sql = "SELECT * FROM kriteria WHERE kode_kriteria = '$kode_kriteria'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo "Kriteria tidak ditemukan.";
    }
} else {
    echo "Kode kriteria tidak ditemukan.";
}

$conn->close();
?>