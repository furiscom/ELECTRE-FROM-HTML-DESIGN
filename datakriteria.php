<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Query untuk mengambil data kriteria
$sql = "SELECT * FROM kriteria";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kriteria</title>
    <link rel="stylesheet" href="Assets/styles/datakriteria.css">
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
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 60%; 
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SPK ELECTRE</h2>
        <ul>
            <li><a href="dasboardadmin.php">Dashboard</a></li>
            <li class="active"><a href="#">Data Kriteria</a></li>
            <li><a href="dataalternatif.php">Data Alternatif</a></li>
            <li><a href="datapenilaian.php">Data Penilaian</a></li>
            <li><a href="dataperhitungan.php">Data Perhitungan</a></li>
            <li><a href="datanilaiakhir.php">Data Hasil Akhir</a></li>
            <li><a href="datapengguna.php">Data Pengguna</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Data Kriteria</h1>
        <div class="card">
            <button class="btn-add" onclick="openModal()">+ Tambah Data</button>

            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Tambah/Edit Data Kriteria</h2>
                    <form id="kriteriaForm">
                        <input type="hidden" id="kode_kriteria_lama" name="kode_kriteria_lama">
                        <label for="kode_kriteria">Kode Kriteria:</label>
                        <input type="text" id="kode_kriteria" name="kode_kriteria" required><br><br>
                        <label for="nama_kriteria">Nama Kriteria:</label>
                        <input type="text" id="nama_kriteria" name="nama_kriteria" required><br><br>
                        <label for="bobot">Bobot:</label>
                        <input type="number" id="bobot" name="bobot" required><br><br>
                        
                        
                        <button type="submit" id="btn-submit">Simpan</button>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kriteria</th>
                        <th>Nama Kriteria</th>
                        <th>Bobot</th>
                       
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
                            echo "<td>" . $row['kode_kriteria'] . "</td>";
                            echo "<td>" . $row['nama_kriteria'] . "</td>";
                            echo "<td>" . $row['bobot'] . "</td>";
                       
                            echo "<td>";
                            echo "<button class='btn-edit' onclick='editKriteria(\"" . $row['kode_kriteria'] . "\")'><i class='fa fa-edit'></i></button>";
                            echo "<button class='btn-delete' onclick='deleteKriteria(\"" . $row['kode_kriteria'] . "\")'><i class='fa fa-trash'></i></button>";
                            echo "</td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>Tidak ada data kriteria.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    // Fungsi untuk membuka modal
    function openModal() {
        document.getElementById("myModal").style.display = "block";
        document.getElementById("kriteriaForm").reset();
        document.getElementById("kode_kriteria_lama").value = ""; // Kosongkan kode_kriteria_lama saat membuka modal
        document.getElementById("btn-submit").textContent = "Simpan"; // Set teks tombol ke "Simpan"
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }

    // Fungsi untuk edit data
    function editKriteria(kode_kriteria) {
        // Ambil data kriteria dari server menggunakan AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_kriteria.php?kode_kriteria=" + kode_kriteria, true);
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var kriteria = JSON.parse(this.responseText);
                // Isi form dengan data yang diterima dari server
                document.getElementById("kode_kriteria_lama").value = kriteria.kode_kriteria;
                document.getElementById("kode_kriteria").value = kriteria.kode_kriteria;
                document.getElementById("nama_kriteria").value = kriteria.nama_kriteria;
                document.getElementById("bobot").value = kriteria.bobot;
                
                // Buka modal setelah form terisi
                document.getElementById("myModal").style.display = "block";
                document.getElementById("btn-submit").textContent = "Update"; // Ubah teks tombol ke "Update"
            }
        };
        xhr.send();
    }

    // Fungsi untuk delete data
    function deleteKriteria(kode_kriteria) {
        if (confirm("Apakah Anda yakin ingin menghapus kriteria ini?")) {
            // Kirim permintaan hapus ke server menggunakan AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_kriteria.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText); // Menampilkan pesan dari server
                    location.reload();
                }
            };
            xhr.send("kode_kriteria=" + kode_kriteria);
        }
    }

    // Event listener untuk form submit (AJAX)
    document.getElementById("kriteriaForm").addEventListener("submit", function(event) {
        event.preventDefault();

        // Ambil data dari form
        var kode_kriteria_lama = document.getElementById("kode_kriteria_lama").value;
        var kode_kriteria = document.getElementById("kode_kriteria").value;
        var nama_kriteria = document.getElementById("nama_kriteria").value;
        var bobot = document.getElementById("bobot").value;
        

        // Kirim data ke server menggunakan AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "simpan_kriteria.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert(this.responseText);
                closeModal();
                location.reload();
            }
        };
        xhr.send("kode_kriteria_lama=" + kode_kriteria_lama + "&kode_kriteria=" + kode_kriteria + "&nama_kriteria=" + nama_kriteria + "&bobot=" + bobot );
    });
</script>

    <?php
    // Tutup koneksi database
    $conn->close();
    ?>
</body>
</html>