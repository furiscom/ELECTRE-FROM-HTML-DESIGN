<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data dari form
$kode_kriteria_lama = $_POST['kode_kriteria_lama'];
$kode_kriteria = $_POST['kode_kriteria'];
$nama_kriteria = $_POST['nama_kriteria'];
$bobot = $_POST['bobot'];


if ($kode_kriteria_lama) {
    // Update data kriteria
    $sql = "UPDATE kriteria SET 
            kode_kriteria = '$kode_kriteria',
            nama_kriteria = '$nama_kriteria',
            bobot = '$bobot'
            
            WHERE kode_kriteria = '$kode_kriteria_lama'";
    $message = "Data kriteria berhasil diupdate!";
} else {
    // Insert data kriteria baru
    $sql = "INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot) 
            VALUES ('$kode_kriteria', '$nama_kriteria', '$bobot')";
    $message = "Data kriteria berhasil ditambahkan!";
}

if ($conn->query($sql) === TRUE) {
    echo $message;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>