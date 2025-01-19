<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_GET['kode_alternatif'])) {
    $kode_alternatif = $_GET['kode_alternatif'];

    // Query untuk mengambil data alternatif berdasarkan kode_alternatif
    $sql = "SELECT * FROM alternatif WHERE kode_alternatif = '$kode_alternatif'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo "Alternatif tidak ditemukan.";
    }
} else {
    echo "Kode alternatif tidak ditemukan.";
}

$conn->close();
?>