<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Query untuk mengambil data alternatif
$sqlAlternatif = "SELECT * FROM alternatif";
$resultAlternatif = $conn->query($sqlAlternatif);

// Query untuk mengambil data kriteria
$sqlKriteria = "SELECT * FROM kriteria";
$resultKriteria = $conn->query($sqlKriteria);

// Query untuk mengambil data penilaian
$sqlPenilaian = "SELECT p.*, a.nama_alternatif, k.nama_kriteria
                FROM penilaian p
                INNER JOIN alternatif a ON p.kode_alternatif = a.kode_alternatif
                INNER JOIN kriteria k ON p.kode_kriteria = k.kode_kriteria";
$resultPenilaian = $conn->query($sqlPenilaian);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Electre - Data Penilaian</title>
    <link rel="stylesheet" href="Assets/styles/penilaian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <div class="logo">
                <span>SPK ELECTRE</span>
            </div>

            <ul class="nav-links">
                <li><a href="dasboardadmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="section-title">MASTER DATA</li>
                <li><a href="datakriteria.php"><i class="fas fa-list"></i> Data Kriteria</a></li>
                <li><a href="dataalternatif.php"><i class="fas fa-users"></i> Data Alternatif</a></li>
                <li class="active"><a href="#"><i class="fas fa-star"></i> Data Penilaian</a></li>
                <li><a href="dataperhitungan.php"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
                <li><a href="datanilaiakhir.php"><i class="fas fa-chart-bar"></i> Data Hasil Akhir</a></li>
                <li class="section-title">MASTER USER</li>
                <li><a href="datapengguna.php"><i class="fas fa-user"></i> Data Pengguna</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <i class="fas fa-star"></i>
                    <h1>Data Penilaian</h1>
                </div>
                <div class="admin-profile">
                    <span>ADMIN</span>
                    <img src="admin-avatar.png" alt="Admin">
                </div>
            </header>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        <span>Daftar Data Penilaian</span>
                        <button class="btn-add" onclick="openModal()">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Alternatif</th>
                                <th>Kriteria</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultPenilaian->num_rows > 0) {
                                $no = 1;
                                while ($row = $resultPenilaian->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no . "</td>";
                                    echo "<td>" . $row['nama_alternatif'] . "</td>";
                                    echo "<td>" . $row['nama_kriteria'] . "</td>";
                                    echo "<td>" . $row['nilai'] . "</td>";
                                    echo "<td>";
                                    echo "<button class=\"btn-edit\"><i class=\"fas fa-edit\"></i></button>";
                                    echo "<button class=\"btn-delete\"><i class=\"fas fa-trash\"></i></button>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data penilaian.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="dataModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Tambah/Edit Data Penilaian</h2>
                    <form id="dataForm">
                        <input type="hidden" id="id_penilaian">
                        <div class="form-group">
                            <label for="kode_alternatif">Alternatif:</label>
                            <select id="kode_alternatif">
                                <?php
                                if ($resultAlternatif->num_rows > 0) {
                                    while ($row = $resultAlternatif->fetch_assoc()) {
                                        echo "<option value='" . $row['kode_alternatif'] . "'>" . $row['nama_alternatif'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode_kriteria">Kriteria:</label>
                            <select id="kode_kriteria">
                                <?php
                                if ($resultKriteria->num_rows > 0) {
                                    while ($row = $resultKriteria->fetch_assoc()) {
                                        echo "<option value='" . $row['kode_kriteria'] . "'>" . $row['nama_kriteria'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nilai">Nilai:</label>
                            <input type="number" id="nilai" required>
                        </div>
                        <button type="submit" class="btn-submit">Simpan</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <?php
    // Tutup koneksi database
    $conn->close();
    ?>

    <script>
        // Fungsi untuk membuka modal
        function openModal() {
            document.getElementById("dataModal").style.display = "block";
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById("dataModal").style.display = "none";
        }

        // Event listener untuk form submit (AJAX)
        document.getElementById("dataForm").addEventListener("submit", function(event) {
            event.preventDefault();

            // Ambil data dari form
            var id_penilaian = document.getElementById("id_penilaian").value;
            var kode_alternatif = document.getElementById("kode_alternatif").value;
            var kode_kriteria = document.getElementById("kode_kriteria").value;
            var nilai = document.getElementById("nilai").value;

            // Kirim data ke server menggunakan AJAX (Anda perlu membuat file PHP terpisah untuk menangani ini)
            // ...
        });
    </script>
</body>
</html>