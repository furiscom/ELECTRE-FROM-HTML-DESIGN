<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: ". $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kodeAlternatif = $_POST["kode_alternatif"];
    $nilai = $_POST['nilai']; // Ambil data nilai

    // Update data penilaian untuk setiap kriteria
    foreach ($nilai as $kodeKriteria => $nilaiKriteria) {
        // Cek apakah data sudah ada
        $sqlCek = "SELECT * FROM penilaian WHERE kode_alternatif = '$kodeAlternatif' AND kode_kriteria = '$kodeKriteria'";
        $resultCek = $conn->query($sqlCek);

        if ($resultCek->num_rows > 0) {
            // Update data
            $sql = "UPDATE penilaian SET nilai = '$nilaiKriteria' WHERE kode_alternatif = '$kodeAlternatif' AND kode_kriteria = '$kodeKriteria'";
            $message = "Data penilaian untuk kriteria '$kodeKriteria' berhasil diperbarui!";
        } else {
            // Insert data baru
            $sql = "INSERT INTO penilaian (kode_alternatif, kode_kriteria, nilai) VALUES ('$kodeAlternatif', '$kodeKriteria', '$nilaiKriteria')";
            $message = "Data penilaian untuk kriteria '$kodeKriteria' berhasil ditambahkan!";
        }

        if ($conn->query($sql) === TRUE) {
            echo $message. "<br>"; // Menampilkan pesan sukses
        } else {
            echo "Error: ". $sql. "<br>". $conn->error; // Menampilkan pesan error jika query gagal
        }
    }

    echo "Semua data penilaian berhasil diperbarui!"; // Menampilkan pesan sukses setelah semua kriteria diproses
}

$conn->close();?>