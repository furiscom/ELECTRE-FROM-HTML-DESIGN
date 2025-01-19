<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data dari form
$id = $_POST['id'];
$email = $_POST['email'];
$password = $_POST['password'];
$nama = $_POST['nama'];
$level = $_POST['level'];

if ($id) {
    // Update data pengguna
    $sql = "UPDATE pengguna SET 
            email = '$email',
            nama = '$nama',
            level = '$level'";
    // Tambahkan password ke query hanya jika diisi
    if (!empty($password)) {
        $sql .= ", password = '$password'"; 
    }
    $sql .= " WHERE id = $id";

    $message = "Data pengguna berhasil diupdate!";
} else {
    // Insert data pengguna baru
    $sql = "INSERT INTO pengguna (email, password, nama, level) 
            VALUES ('$email', '$password', '$nama', '$level')";
    $message = "Data pengguna berhasil ditambahkan!";
}

if ($conn->query($sql) === TRUE) {
    echo $message;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>