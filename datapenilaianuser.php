<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: ". $conn->connect_error);
}

// Ambil data alternatif
$sqlAlternatif = "SELECT * FROM alternatif";
$resultAlternatif = $conn->query($sqlAlternatif);

// Ambil data kriteria
$sqlKriteria = "SELECT nama_kriteria,kode_kriteria FROM kriteria"; // Hanya mengambil nama kriteria
$resultKriteria = $conn->query($sqlKriteria);

// Ambil data penilaian
$sqlPenilaian = "SELECT p.*, a.nama_alternatif, k.nama_kriteria
                FROM penilaian p
                INNER JOIN alternatif a ON p.kode_alternatif = a.kode_alternatif
                INNER JOIN kriteria k ON p.kode_kriteria = k.kode_kriteria";
$resultPenilaian = $conn->query($sqlPenilaian);

// Inisialisasi array untuk menyimpan data penilaian
$penilaian =[];
while ($row = $resultPenilaian->fetch_assoc()) {
    $penilaian[$row['nama_alternatif']][$row['nama_kriteria']] = $row['nilai'];
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Electre - Data Penilaian</title>
    <link rel="stylesheet" href="Assets/styles/penilaian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS untuk modal */
      .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

      .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
        }

      .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

      .form-group {
            margin-bottom: 15px;
        }

      .form-group label {
            display: block;
            margin-bottom: 5px;
        }

      .form-group input,
      .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

      .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <div class="logo">
                <span>SPK ELECTRE</span>
            </div>

            <ul class="nav-links">
                <li><a href="dasboardadmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="#"><i class="fas fa-star"></i> Data Penilaian</a></li>
                <li><a href="datanilaiakhiruser.php"><i class="fas fa-chart-bar"></i> Data Hasil Akhir</a></li>
                <li><a href="logout.php">Logout</a></li>
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
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Alternatif</th>
                                <?php
                                // Fetch kriteria names
                                $resultKriteria = $conn->query($sqlKriteria);

                                // Display kriteria names as column headers
                                while ($rowKriteria = $resultKriteria->fetch_assoc()) {
                                    echo "<th>{$rowKriteria['nama_kriteria']}</th>";
                                }
                              ?>
                                <!-- <th>Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Iterasi melalui data alternatif
                            while ($rowAlternatif = $resultAlternatif->fetch_assoc()) {
                                $kodeAlternatif = $rowAlternatif['kode_alternatif'];
                                $namaAlternatif = $rowAlternatif['nama_alternatif'];
                                echo "<tr>";
                                echo "<td>". $no++. "</td>";
                                echo "<td>". $namaAlternatif. "</td>";

                                // Iterasi melalui data kriteria
                                $resultKriteria->data_seek(0); // Reset pointer result kriteria
                                while ($rowKriteria = $resultKriteria->fetch_assoc()) {
                                    $kodeKriteria = $rowKriteria['kode_kriteria'];
                                    $namaKriteria = $rowKriteria['nama_kriteria'];
                                    $nilai = isset($penilaian[$namaAlternatif][$namaKriteria])? $penilaian[$namaAlternatif][$namaKriteria]: '';

                                    echo "<td>";
                                    if ($nilai!== '') {
                                        echo $nilai;
                                    } else {
                                        // Menampilkan tombol "Beri Nilai" jika belum ada nilai
                                        echo "<button class='btn-edit' onclick=\"openModal('$kodeAlternatif', '$kodeKriteria')\">Beri Nilai</button>";
                                    }
                                    echo "</td>";
                                }

                                // Menampilkan tombol edit 
                                echo "<td>";
                                // echo "<button class='btn-edit' onclick=\"openModal('$kodeAlternatif')\"><i class='fas fa-edit'></i></button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                          ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="dataModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Beri Penilaian</h2>
                    <form id="dataForm">
                        <input type="hidden" id="kode_alternatif" name="kode_alternatif">
                        <input type="hidden" id="kode_kriteria" name="kode_kriteria">
                        <div class="form-group">
                            <label for="nilai">Nilai:</label>
                            <input type="number" id="nilai" name="nilai" required>
                        </div>
                        <button type="submit" class="btn-submit">Simpan</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        function openModal(kodeAlternatif = null, kodeKriteria = null) {
            document.getElementById('dataModal').style.display = 'block';
            if (kodeAlternatif!== null) {
                document.getElementById('kode_alternatif').value = kodeAlternatif;
            }
            if (kodeKriteria!== null) {
                document.getElementById('kode_kriteria').value = kodeKriteria;
            }
        }

        function closeModal() {
            document.getElementById('dataModal').style.display = 'none';
        }

        document.getElementById('dataForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var kodeAlternatif = document.getElementById('kode_alternatif').value;
            var kodeKriteria = document.getElementById('kode_kriteria').value;
            var nilai = document.getElementById('nilai').value;

            // Kirim data ke server menggunakan AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "simpan_penilaian.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                    closeModal();
                    location.reload();
                }
            };
            xhr.send("kode_alternatif=" + kodeAlternatif + "&kode_kriteria=" + kodeKriteria + "&nilai=" + nilai);
        });
    </script>
</body>
</html>