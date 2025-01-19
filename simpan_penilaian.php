<?php
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$id_penilaian = $_POST['id_penilaian'];
$kode_alternatif = $_POST['kode_alternatif'];
$kode_kriteria = $_POST['kode_kriteria'];
$nilai = $_POST['nilai'];

if ($id_penilaian) { // Jika id_penilaian ada, lakukan UPDATE
    $sql = "UPDATE penilaian SET
                kode_alternatif = '$kode_alternatif',
                kode_kriteria = '$kode_kriteria',
                nilai = $nilai
            WHERE id_penilaian = $id_penilaian";
    $message = "Penilaian berhasil diupdate!";
} else { // Jika id_penilaian tidak ada, lakukan INSERT
    // Ambil id_penilaian terakhir
    $sqlLastId = "SELECT MAX(id_penilaian) as last_id FROM penilaian";
    $resultLastId = $conn->query($sqlLastId);
    $rowLastId = $resultLastId->fetch_assoc();
    $lastId = $rowLastId['last_id'];

    // Hitung id_penilaian baru
    $newId = $lastId + 1;

    $sql = "INSERT INTO penilaian (id_penilaian, kode_alternatif, kode_kriteria, nilai) 
            VALUES ($newId, '$kode_alternatif', '$kode_kriteria', $nilai)";
    $message = "Penilaian berhasil ditambahkan!";
}

if ($conn->query($sql) === TRUE) {
    echo $message;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>