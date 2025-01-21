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
$peringkat = [];
$alternatif_tersisa = $alternatif;
while (!empty($alternatif_tersisa)) {
    $alternatif_dominan_temp = alternatifDominan($matrix_agregat);

    // Jika tidak ada alternatif dominan, hentikan perulangan
    if (empty($alternatif_dominan_temp)) {
        break;
    }

    foreach ($alternatif_dominan_temp as $kode_alternatif) {
        $peringkat[] = $kode_alternatif;
        unset($alternatif_tersisa[$kode_alternatif]);
        // Hapus baris dan kolom alternatif dominan dari matriks agregat
        unset($matrix_agregat[$kode_alternatif]);
        foreach ($matrix_agregat as $key => $value) {
            unset($matrix_agregat[$key][$kode_alternatif]);
        }
    }
}
// --- Akhir perankingan ---

// --- Menentukan alternatif terbaik ---

// Hitung skor akhir untuk setiap alternatif
$skor_akhir = [];
foreach ($peringkat as $i => $kode_alternatif) {
    $skor_akhir[$kode_alternatif] = count($peringkat) - $i;
}

// Urutkan alternatif berdasarkan skor akhir
arsort($skor_akhir);

// Ambil alternatif terbaik (dengan skor tertinggi)
$alternatif_terbaik = key($skor_akhir);

// --- Akhir penentuan alternatif terbaik ---

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hasil Akhir - SPK ELECTRE</title>
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
            <h1>Data Hasil Akhir</h1>
            <button class="cetak" onclick="cetakData()">Cetak Data</button>
        </div>

        <div class="table-container">
            <h2>Data Hasil Perankingan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Alternatif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($peringkat as $kode_alternatif):
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $alternatif[$kode_alternatif]['nama_alternatif']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2>Alternatif Terbaik</h2>
            <p><?php echo $alternatif[$alternatif_terbaik]['nama_alternatif']; ?></p>
        </div>
    </div>
</body>
</html>