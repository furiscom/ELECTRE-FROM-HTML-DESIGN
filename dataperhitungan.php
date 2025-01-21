<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data kriteria
$sql = "SELECT * FROM kriteria";
$result = $conn->query($sql);
$kriteria = [];
while ($row = $result->fetch_assoc()) {
    $kriteria[$row['kode_kriteria']] = $row;
}

// Ambil data alternatif
$sql = "SELECT * FROM alternatif";
$result = $conn->query($sql);
$alternatif = [];
while ($row = $result->fetch_assoc()) {
    $alternatif[$row['kode_alternatif']] = $row;
}

// Ambil data penilaian
$sql = "SELECT * FROM penilaian"; 
$result = $conn->query($sql);
$penilaian = [];
while ($row = $result->fetch_assoc()) {
    $penilaian[$row['kode_alternatif']][$row['kode_kriteria']] = $row['nilai'];
}

// --- Fungsi untuk perhitungan ELECTRE ---

// Fungsi normalisasi
function normalisasi($penilaian, $alternatif, $kriteria) {
    $matrix = [];
    foreach ($kriteria as $kode_kriteria => $data_kriteria) {
        $sum = 0;
        foreach ($alternatif as $kode_alternatif => $data_alternatif) {
            // Gunakan isset() untuk memeriksa apakah elemen array ada
            $nilai = isset($penilaian[$kode_alternatif][$kode_kriteria]) ? $penilaian[$kode_alternatif][$kode_kriteria] : 0;
            $sum += pow($nilai, 2);
        }
        $sqrt_sum = sqrt($sum);
        foreach ($alternatif as $kode_alternatif => $data_alternatif) {
            $nilai = isset($penilaian[$kode_alternatif][$kode_kriteria]) ? $penilaian[$kode_alternatif][$kode_kriteria] : 0;
            $matrix[$kode_alternatif][$kode_kriteria] = $nilai / $sqrt_sum;
        }
    }
    return $matrix;
}

// Fungsi terbobot
function terbobot($matrix_normalisasi, $kriteria) {
    $matrix = [];
    foreach ($matrix_normalisasi as $kode_alternatif => $nilai_alternatif) {
        foreach ($nilai_alternatif as $kode_kriteria => $nilai) {
            $matrix[$kode_alternatif][$kode_kriteria] = $nilai * $kriteria[$kode_kriteria]['bobot'];
        }
    }
    return $matrix;
}

// Fungsi concordance
function concordance($matrix_terbobot, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);
    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $nilai_concordance = 0;
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    if (
                        ($data_kriteria['jenis'] == 'benefit' && $matrix_terbobot[$alternatif_a][$kode_kriteria] >= $matrix_terbobot[$alternatif_b][$kode_kriteria]) ||
                        ($data_kriteria['jenis'] == 'cost' && $matrix_terbobot[$alternatif_a][$kode_kriteria] <= $matrix_terbobot[$alternatif_b][$kode_kriteria])

                    ) {
                        $nilai_concordance += $data_kriteria['bobot'];
                    }
                }
                $matrix[$alternatif_a][$alternatif_b] = $nilai_concordance;
            }
        }
    }
    return $matrix;
}

// Fungsi discordance
function discordance($matrix_terbobot, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);
    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $nilai_discordance = 0;
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    if (isset($matrix_terbobot[$alternatif_a][$kode_kriteria]) && isset($matrix_terbobot[$alternatif_b][$kode_kriteria])) {
                        $selisih = abs($matrix_terbobot[$alternatif_a][$kode_kriteria] - $matrix_terbobot[$alternatif_b][$kode_kriteria]);
                        if ($selisih > $nilai_discordance) {
                            $nilai_discordance = $selisih;
                        }
                    }
                }
                $matrix[$alternatif_a][$alternatif_b] = $nilai_discordance;
            }
        }
    }
    return $matrix;
}

// Fungsi threshold
function threshold($matrix) {
    $total = 0;
    $count = 0;
    foreach ($matrix as $row) {
        foreach ($row as $value) {
            $total += $value;
            $count++;
        }
    }
    return $total / $count;
}

// Fungsi dominasi
function dominasi($matrix, $threshold) {
    $matrix_dominasi = [];
    foreach ($matrix as $alternatif_a => $row) {
        foreach ($row as $alternatif_b => $value) {
            $matrix_dominasi[$alternatif_a][$alternatif_b] = $value >= $threshold ? 1 : 0;
        }
    }
    return $matrix_dominasi;
}

// Fungsi agregat
function agregat($matrix_dominasi_concordance, $matrix_dominasi_discordance) {
    $matrix = [];
    foreach ($matrix_dominasi_concordance as $alternatif_a => $row) {
        foreach ($row as $alternatif_b => $value) {
            $matrix[$alternatif_a][$alternatif_b] = $value * $matrix_dominasi_discordance[$alternatif_a][$alternatif_b];
        }
    }
    return $matrix;
}

// Fungsi alternatifDominan
function alternatifDominan($matrix_agregat) {
    $alternatif_dominan = [];
    foreach ($matrix_agregat as $alternatif_a => $row) {
        $dominan = true;
        foreach ($row as $alternatif_b => $value) {
            if ($value == 0) {
                $dominan = false;
                break;
            }
        }
        if ($dominan) {
            $alternatif_dominan[] = $alternatif_a;
        }
    }
    return $alternatif_dominan;
}

// --- Akhir fungsi untuk perhitungan ELECTRE ---

// --- Lakukan perhitungan ---
$matrix_ternormalisasi = normalisasi($penilaian, $alternatif, $kriteria);
$matrix_terbobot = terbobot($matrix_ternormalisasi, $kriteria);
$matrix_concordance = concordance($matrix_terbobot, $kriteria);
$matrix_discordance = discordance($matrix_terbobot, $kriteria);

$threshold_concordance = threshold($matrix_concordance);
$threshold_discordance = threshold($matrix_discordance);

$matrix_dominasi_concordance = dominasi($matrix_concordance, $threshold_concordance);
$matrix_dominasi_discordance = dominasi($matrix_discordance, $threshold_discordance);

$matrix_agregat = agregat($matrix_dominasi_concordance, $matrix_dominasi_discordance);
$alternatif_dominan = alternatifDominan($matrix_agregat);

// --- Perankingan ---

// Query untuk mengambil data hasil perankingan (sama seperti di datanilaiakhir.php)
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

// Simpan hasil perankingan ke dalam array
$peringkat = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $peringkat[] = $row;
    }
}

// --- Akhir perankingan ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Electre - Data Perhitungan</title>
    <link rel="stylesheet" href="Assets/styles/dataalternatif.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Gaya CSS untuk tabel */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f0f0f0;
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
                <li><a href="dataalternatif.php"><i class="fas fa-users"></i> Data Alternatif</a></li>
                <li><a href="datapenilaian.php"><i class="fas fa-star"></i> Data Penilaian</a></li>
                <li class="active"><a href="#"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
                <li><a href="datanilaiakhir.php"><i class="fas fa-chart-bar"></i> Data Hasil Akhir</a></li>
                <li class="section-title">MASTER USER</li>
                <li><a href="datapengguna.php"><i class="fas fa-user"></i> Data Pengguna</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <i class="fas fa-calculator"></i>
                    <h1>Data Perhitungan</h1>
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
                        <span>Tabel Perhitungan ELECTRE</span>
                    </div>

                    <h3>Matriks Ternormalisasi</h3>
                    <?php echo print_table($matrix_ternormalisasi, $alternatif, $kriteria); ?>

                    <h3>Matriks Terbobot</h3>
                    <?php echo print_table($matrix_terbobot, $alternatif, $kriteria); ?>

                    <h3>Matriks Concordance</h3>
                    <?php echo print_table($matrix_concordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Discordance</h3>
                    <?php echo print_table($matrix_discordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Dominasi Concordance (Threshold: <?php echo $threshold_concordance; ?>)</h3>
                    <?php echo print_table($matrix_dominasi_concordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Dominasi Discordance (Threshold: <?php echo $threshold_discordance; ?>)</h3>
                    <?php echo print_table($matrix_dominasi_discordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Agregat Dominasi</h3>
                    <?php echo print_table($matrix_agregat, $alternatif, $alternatif); ?>

                    <h3>Hasil Perankingan</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Alternatif</th>
                                <th>Total</th>
                                <th>Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peringkat as $row): ?>
                                <tr>
                                    <td><?php echo $row['nama_alternatif']; ?></td>
                                    <td><?php echo $row['total']; ?></td>
                                    <td><?php echo $row['rank']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </main>
    </div>

    <?php
    // Fungsi untuk menampilkan data dalam bentuk tabel
    function print_table($data, $row_labels, $col_labels = null) {
        $output = "<table class='data-table'>";
        // Header tabel
        $output .= "<thead><tr>";
        if ($col_labels) {
            $output .= "<th></th>"; // Sel kosong di pojok kiri atas
            foreach ($col_labels as $col_key => $col_label) {
                $output .= "<th>" . (is_array($col_label) && isset($col_label['nama_kriteria']) ? $col_label['nama_kriteria'] : $col_key) . "</th>";
            }
        }
        $output .= "</tr></thead>";
        // Body tabel
        $output .= "<tbody>";
        foreach ($row_labels as $row_key => $row_label) {
            $output .= "<tr>";
            $output .= "<th>" . (is_array($row_label) ? $row_label['nama_alternatif'] : $row_key) . "</th>";
            if ($col_labels) {
                foreach ($col_labels as $col_key => $col_label) {
                    $output .= "<td>" . (isset($data[$row_key][$col_key]) ? $data[$row_key][$col_key] : '-') . "</td>";
                }
            } else {
                foreach ($data[$row_key] as $value) {
                    $output .= "<td>$value</td>";
                }
            }
            $output .= "</tr>";
        }
        $output .= "</tbody>";
        $output .= "</table>";
        return $output;
    }
    ?>
</body>
</html>