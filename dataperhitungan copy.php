<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data kriteria
$sql = "SELECT * FROM kriteria order by id";
$result = $conn->query($sql);
$kriteria = [];
while ($row = $result->fetch_assoc()) {
    $kriteria[$row['kode_kriteria']] = $row;
}

// Ambil data alternatif
$sql = "SELECT * FROM alternatif order by id";
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
                $max_all_diff = 0; // Inisialisasi di luar loop kriteria

                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    if (isset($matrix_terbobot[$alternatif_a][$kode_kriteria]) && isset($matrix_terbobot[$alternatif_b][$kode_kriteria])) {
                        $selisih = abs($matrix_terbobot[$alternatif_a][$kode_kriteria] - $matrix_terbobot[$alternatif_b][$kode_kriteria]);
                        if ($selisih > $nilai_discordance) {
                            $nilai_discordance = $selisih;
                        }
                        $max_all_diff = max($max_all_diff, $selisih); // Perbarui max_all_diff
                    }
                }
                // Hindari pembagian dengan nol
                $matrix[$alternatif_a][$alternatif_b] = $max_all_diff != 0 ? $nilai_discordance / $max_all_diff : 0;
            }
        }
    }
    return $matrix;
}


function himpunan_concordance($matrix_terbobot, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);
    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $c = [];
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {

                    if (
                        ($data_kriteria['jenis'] == 'benefit' && $matrix_terbobot[$alternatif_a][$kode_kriteria] >= $matrix_terbobot[$alternatif_b][$kode_kriteria]) ||
                        ($data_kriteria['jenis'] == 'cost' && $matrix_terbobot[$alternatif_a][$kode_kriteria] <= $matrix_terbobot[$alternatif_b][$kode_kriteria])

                    ) {
                        $c[] = $kode_kriteria;
                    }
                }
                $matrix[$alternatif_a][$alternatif_b] = $c;
            }
        }
    }
    return $matrix;
}


function himpunan_discordance($matrix_terbobot, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);
    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $d = [];
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    if (
                        ($data_kriteria['jenis'] == 'benefit' && $matrix_terbobot[$alternatif_a][$kode_kriteria] < $matrix_terbobot[$alternatif_b][$kode_kriteria]) ||
                        ($data_kriteria['jenis'] == 'cost' && $matrix_terbobot[$alternatif_a][$kode_kriteria] > $matrix_terbobot[$alternatif_b][$kode_kriteria])

                    ) {
                        $d[] = $kode_kriteria;
                    }
                }
                $matrix[$alternatif_a][$alternatif_b] = $d;
            }
        }
    }
    return $matrix;
}


function matriks_concordance($himpunan_concordance, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($himpunan_concordance);
    $jumlah_alternatif = count($alternatif_keys);

    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $nilai_concordance = 0;
                foreach ($himpunan_concordance[$alternatif_a][$alternatif_b] as $kode_kriteria) {
                    $nilai_concordance += $kriteria[$kode_kriteria]['bobot'];
                }
                $matrix[$alternatif_a][$alternatif_b] = $nilai_concordance;
            }
        }
    }
    return $matrix;
}

function matriks_discordance($matrix_terbobot, $himpunan_discordance, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);

    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $max_diff_discordance = 0;
                foreach ($himpunan_discordance[$alternatif_a][$alternatif_b] as $kode_kriteria) {
                    $diff = abs($matrix_terbobot[$alternatif_a][$kode_kriteria] - $matrix_terbobot[$alternatif_b][$kode_kriteria]);
                    if ($diff > $max_diff_discordance) {
                        $max_diff_discordance = $diff;
                    }
                }

                $max_diff_all = 0;
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    $diff = abs($matrix_terbobot[$alternatif_a][$kode_kriteria] - $matrix_terbobot[$alternatif_b][$kode_kriteria]);
                    if ($diff > $max_diff_all) {
                        $max_diff_all = $diff;
                    }
                }
                // Hindari pembagian dengan nol
                $matrix[$alternatif_a][$alternatif_b] = $max_diff_all != 0 ? $max_diff_discordance / $max_diff_all : 0;
            }
        }
    }

    return $matrix;
}



function threshold($matrix) {
    $sum = 0;
    $count = 0;
    foreach ($matrix as $row) {
        foreach ($row as $value) {
            $sum += $value;
            $count++;
        }
    }
    return $count > 0 ? $sum / $count : 0; // Handle kasus $count = 0
}

function matriks_dominan($matrix, $threshold) {
    $matrix_dominan = [];
    foreach ($matrix as $k => $row) {
        foreach ($row as $l => $value) {
            $matrix_dominan[$k][$l] = $value >= $threshold ? 1 : 0;
        }
    }
    return $matrix_dominan;
}

function matriks_dominan_keseluruhan($matrix_dominan_concordance, $matrix_dominan_discordance) {
    $E = [];
    foreach ($matrix_dominan_concordance as $k => $row) {
        foreach ($row as $l => $value) {
            $E[$k][$l] = $value * $matrix_dominan_discordance[$k][$l];
        }
    }
    return $E;
}

// --- Akhir fungsi untuk perhitungan ELECTRE ---


// --- Lakukan perhitungan ---
$matrix_normalisasi = normalisasi($penilaian, $alternatif, $kriteria);
$matrix_terbobot = terbobot($matrix_normalisasi, $kriteria);
$himpunan_concordance = himpunan_concordance($matrix_terbobot, $kriteria);
$himpunan_discordance = himpunan_discordance($matrix_terbobot, $kriteria);
$matrix_concordance = matriks_concordance($himpunan_concordance, $kriteria);
$matrix_discordance = matriks_discordance($matrix_terbobot, $himpunan_discordance, $kriteria);
$threshold_concordance = threshold($matrix_concordance);
$threshold_discordance = threshold($matrix_discordance);
$matrix_dominan_concordance = matriks_dominan($matrix_concordance, $threshold_concordance);
$matrix_dominan_discordance = matriks_dominan($matrix_discordance, $threshold_discordance);
$matrix_agregat = matriks_dominan_keseluruhan($matrix_dominan_concordance, $matrix_dominan_discordance);

// --- Perankingan ---
// ... (kode perankingan tetap sama)
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

                    <h3>Matriks Normalisasi</h3>
                    <?php echo print_table($matrix_normalisasi); ?>

                    <h3>Matriks Terbobot</h3>
                    <?php echo print_table($matrix_terbobot); ?>
                    <h3>Himpunan Concordance:</h3>
                    <?php echo print_table($himpunan_concordance, $alternatif, $alternatif); // Menggunakan fungsi print_table()?>

                    <h3>Himpunan Discordance:</h3>
                    <?php echo print_table($himpunan_discordance, $alternatif, $alternatif); // Menggunakan fungsi print_table()?>
 
                    <h3>Matriks Concordance</h3>
                    <?php echo print_table($matrix_concordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Discordance</h3>
                    <?php echo print_table($matrix_discordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Dominan Concordance (Threshold: <?php echo $threshold_concordance; ?>)</h3>
                    <?php echo print_table($matrix_dominan_concordance, $alternatif, $alternatif); ?>

                    <h3>Matriks Dominan Discordance (Threshold: <?php echo $threshold_discordance; ?>)</h3>
                    <?php echo print_table($matrix_dominan_discordance, $alternatif, $alternatif); ?>

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
    function print_table($data, $row_labels = null, $col_labels = null) {
        $output = "<table class='data-table'>";

        // Header tabel (jika ada label kolom)
        if ($col_labels) {
            $output .= "<thead><tr><th></th>"; // Sel kosong di pojok kiri atas
            foreach ($col_labels as $col_key => $col_label) {
                $label = is_array($col_label) ? $col_label['nama_alternatif'] : $col_key;
                $output .= "<th>" . htmlspecialchars($label) . "</th>"; // Escape label for security
            }
            $output .= "</tr></thead>";
        }

        // Body tabel
        $output .= "<tbody>";

        // Check if $data is empty.  This prevents warnings if there's no data to display.
        if (empty($data)) {
            $output .= "<tr><td colspan='" . (count($col_labels) + 1) . "'>No data available</td></tr>";
        } else {
            foreach ($data as $row_key => $row) {
                $output .= "<tr>";

                // Label baris (jika ada)
                if ($row_labels) {
                    $label = is_array($row_labels[$row_key]) ? $row_labels[$row_key]['nama_alternatif'] : $row_key;
                    $output .= "<th>" . htmlspecialchars($label) . "</th>"; // Escape label for security
                }

                // Nilai sel
                foreach ($row as $value) {
                    if (is_array($value)) {
                        $output .= "<td>" . htmlspecialchars(implode(', ', $value)) . "</td>"; // Escape and join array values
                    } else {
                        $output .= "<td>" . (isset($value) ? htmlspecialchars($value) : '-') . "</td>"; // Escape value or display '-' if not set
                    }
                }
                $output .= "</tr>";
            }
        }


        $output .= "</tbody>";
        $output .= "</table>";
        return $output;
    }
    ?>
</body>  
</html>