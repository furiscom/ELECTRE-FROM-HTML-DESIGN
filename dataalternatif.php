<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Query untuk mengambil data alternatif
$sql = "SELECT * FROM alternatif";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Electre - Data Alternatif</title>
    <link rel="stylesheet" href="Assets/styles/dataalternatif.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Tambahkan CSS untuk modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
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
                <li class="section-title">MASTER DATA</li>
                <li><a href="datakriteria.php"><i class="fas fa-list"></i> Data Kriteria</a></li>
                <li class="active"><a href="#"><i class="fas fa-users"></i> Data Alternatif</a></li>
                <li><a href="datapenilaian.php"><i class="fas fa-star"></i> Data Penilaian</a></li>
                <li><a href="#"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
                <li><a href="datanilaiakhir.php"><i class="fas fa-chart-bar"></i> Data Hasil Akhir</a></li>
                <li class="section-title">MASTER USER</li>
                <li><a href="datapengguna.php"><i class="fas fa-user"></i> Data Pengguna</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <i class="fas fa-users"></i>
                    <h1>Data Alternatif</h1>
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
                        <span>Daftar Data Alternatif</span>
                        <button class="btn-add" onclick="openModal()">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>

                    <div class="table-controls">
                        <div class="entries-control">
                            Show
                            <select>
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select>
                            entries
                        </div>
                        <div class="search-control">
                            Search: <input type="text" placeholder="Search...">
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Alternatif</th>
                                <th>Nama Alternatif</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no . "</td>";
                                    echo "<td>" . $row['kode_alternatif'] . "</td>";
                                    echo "<td>" . $row['nama_alternatif'] . "</td>";
                                    echo "<td>";
                                    echo "<button class=\"btn-edit\" onclick=\"editAlternatif('" . $row['kode_alternatif'] . "')\"><i class=\"fas fa-edit\"></i></button>";
                                    echo "<button class=\"btn-delete\" onclick=\"deleteAlternatif('" . $row['kode_alternatif'] . "')\"><i class=\"fas fa-trash\"></i></button>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4'>Tidak ada data alternatif.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="dataModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Tambah/Edit Data Alternatif</h2>
                    <form id="dataForm">
                        <input type="hidden" id="kode_alternatif_lama" name="kode_alternatif_lama">
                        <div class="form-group">
                            <label for="kode_alternatif">Kode Alternatif:</label>
                            <input type="text" id="kode_alternatif" name="kode_alternatif" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_alternatif">Nama Alternatif:</label>
                            <input type="text" id="nama_alternatif" name="nama_alternatif" required>
                        </div>
                        <button type="submit" id="btn-submit">Simpan</button>
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
            document.getElementById("dataForm").reset(); // Reset form saat membuka modal
            document.getElementById("kode_alternatif_lama").value = ""; // Kosongkan field kode_alternatif_lama
            document.getElementById("btn-submit").textContent = "Simpan"; // Set teks tombol ke "Simpan"
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById("dataModal").style.display = "none";
        }

        // Fungsi untuk edit data
        function editAlternatif(kode_alternatif) {
            // Ambil data alternatif dari server menggunakan AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_alternatif.php?kode_alternatif=" + kode_alternatif, true);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var alternatif = JSON.parse(this.responseText);
                    // Isi form dengan data yang diterima dari server
                    document.getElementById("kode_alternatif_lama").value = alternatif.kode_alternatif;
                    document.getElementById("kode_alternatif").value = alternatif.kode_alternatif;
                    document.getElementById("nama_alternatif").value = alternatif.nama_alternatif;
                    // Buka modal setelah form terisi
                    document.getElementById("dataModal").style.display = "block";
                    document.getElementById("btn-submit").textContent = "Update"; // Ubah teks tombol ke "Update"
                }
            };
            xhr.send();
        }

        // Fungsi untuk delete data
        function deleteAlternatif(kode_alternatif) {
            if (confirm("Apakah Anda yakin ingin menghapus alternatif ini?")) {
                // Kirim permintaan hapus ke server menggunakan AJAX (Anda perlu membuat file PHP terpisah untuk menangani ini)
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_alternatif.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        alert(this.responseText);
                        location.reload();
                    }
                };
                xhr.send("kode_alternatif=" + kode_alternatif);
            }
        }

        // Event listener untuk form submit (AJAX)
        document.getElementById("dataForm").addEventListener("submit", function(event) {
            event.preventDefault();

            // Ambil data dari form
            var kode_alternatif_lama = document.getElementById("kode_alternatif_lama").value;
            var kode_alternatif = document.getElementById("kode_alternatif").value;
            var nama_alternatif = document.getElementById("nama_alternatif").value;

            // Kirim data ke server menggunakan AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "simpan_alternatif.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                    closeModal();
                    location.reload();
                }
            };
            xhr.send("kode_alternatif_lama=" + kode_alternatif_lama + "&kode_alternatif=" + kode_alternatif + "&nama_alternatif=" + nama_alternatif);
        });
    </script>
</body>
</html>