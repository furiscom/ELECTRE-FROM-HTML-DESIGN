<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data dari form
$kode_alternatif_lama = $_POST['kode_alternatif_lama'];
$kode_alternatif = $_POST['kode_alternatif'];
$nama_alternatif = $_POST['nama_alternatif'];

if ($kode_alternatif_lama) {
    // Update data alternatif
    $sql = "UPDATE alternatif SET 
            kode_alternatif = '$kode_alternatif',
            nama_alternatif = '$nama_alternatif'
            WHERE kode_alternatif = '$kode_alternatif_lama'";
    $message = "Data alternatif berhasil diupdate!";
} else {
    // Insert data alternatif baru
    $sql = "INSERT INTO alternatif (kode_alternatif, nama_alternatif) 
            VALUES ('$kode_alternatif', '$nama_alternatif')";
    $message = "Data alternatif berhasil ditambahkan!";
}

if ($conn->query($sql) === TRUE) {
    echo $message;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>