<?php
// Koneksi ke database (ganti dengan konfigurasi database Anda)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "electre";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data hasil perankingan
$sql = "SELECT
            a.nama_alternatif,
            SUM(p.nilai * k.bobot) AS total,
            (
                SELECT COUNT(*) + 1
                FROM (
                    SELECT a2.kode_alternatif, SUM(p2.nilai * k2.bobot) AS total2
                    FROM alternatif a2
                    INNER JOIN penilaian p2 ON a2.kode_alternatif = p2.kode_alternatif
                    INNER JOIN kriteria k2 ON p2.kode_kriteria = k2.kode_kriteria
                    GROUP BY a2.kode_alternatif
                ) AS sub
                WHERE sub.total2 > SUM(p.nilai * k.bobot)
            ) AS `rank`
        FROM
            alternatif a
        INNER JOIN
            penilaian p ON a.kode_alternatif = p.kode_alternatif
        INNER JOIN
            kriteria k ON p.kode_kriteria = k.kode_kriteria
        GROUP BY
            a.nama_alternatif";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nilai Akhir - SPK ELECTRE</title>
    <link rel="stylesheet" href="Assets/styles/nilaiakhir.css">
    <script>
        function cetakData() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>SPK ELECTRE</h2>
        <ul>
            <li><a href="dasboardadmin.php">Dashboard</a></li>
            <li>Master Data
                <ul>
                    <li><a href="datakriteria.php">Data Kriteria</a></li>
                    <li><a href="dataalternatif.php">Data Alternatif</a></li>
                </ul>
            </li>
            <li><a href="datapenilaian.php">Data Penilaian</a></li>
            <li><a href="dataperhitungan.php">Data Perhitungan</a></li>
            <li class="active"><a href="#">Data Hasil Akhir</a></li>
            <li>Master User
                <ul>
                    <li><a href="datapengguna.php">Data Pengguna</a></li>
                </ul>
            </li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>Data Nilai Akhir</h1>
            <button class="cetak" onclick="cetakData()">Cetak Data</button>
        </div>

        <div class="table-container">
            <h2>Data Hasil Perankingan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Alternatif</th>
                        <th>Total</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["nama_alternatif"]."</td>";
                            echo "<td>".$row["total"]."</td>";
                            echo "<td>".$row["rank"]."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Tidak ada data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>