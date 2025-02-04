<?php

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

// Fungsi discordance (diubah sesuai kode Python)
function discordance($matrix_terbobot, $kriteria) {
    $matrix = [];
    $alternatif_keys = array_keys($matrix_terbobot);
    $jumlah_alternatif = count($alternatif_keys);
    $delta = 0; // Inisialisasi delta

    // Hitung Delta (perbedaan maksimum antar nilai kriteria)
    foreach ($kriteria as $kode_kriteria => $data_kriteria) {
        $max_diff = 0;
        foreach ($alternatif_keys as $alt1) {
            foreach ($alternatif_keys as $alt2) {
               if(isset($matrix_terbobot[$alt1][$kode_kriteria]) && isset($matrix_terbobot[$alt2][$kode_kriteria])){
                $diff = abs($matrix_terbobot[$alt1][$kode_kriteria] - $matrix_terbobot[$alt2][$kode_kriteria]);
                $max_diff = max($max_diff, $diff);
               }
                
            }
        }
        $delta = max($delta, $max_diff);
    }


    for ($i = 0; $i < $jumlah_alternatif; $i++) {
        $alternatif_a = $alternatif_keys[$i];
        for ($j = 0; $j < $jumlah_alternatif; $j++) {
            $alternatif_b = $alternatif_keys[$j];
            if ($alternatif_a != $alternatif_b) {
                $nilai_discordance = 0;
                foreach ($kriteria as $kode_kriteria => $data_kriteria) {
                    if (isset($matrix_terbobot[$alternatif_a][$kode_kriteria]) && isset($matrix_terbobot[$alternatif_b][$kode_kriteria])) {
                        $selisih = abs($matrix_terbobot[$alternatif_a][$kode_kriteria] - $matrix_terbobot[$alternatif_b][$kode_kriteria]);
                        
                        if($delta != 0){
                            $nilai_discordance = max($nilai_discordance, $selisih / $delta); // Normalisasi dengan delta
                        }else{
                            $nilai_discordance = 0;
                        }
                        
                    }
                }
                $matrix[$alternatif_a][$alternatif_b] = $nilai_discordance;
            }
        }
    }
    return $matrix;
}
function concordance_discordance($data_dict, $weights) {
    $delta = __get_delta($data_dict);
    $alternatives = array_keys($data_dict);
    $num_alternatives = count($alternatives);

    $concordance_matrix = array_fill(0, $num_alternatives, array_fill(0, $num_alternatives, 0)); // Initialize with 0s
    $discordance_matrix = array_fill(0, $num_alternatives, array_fill(0, $num_alternatives, 0));

    // Iterate through all pairs of alternatives (combinations)
    for ($i = 0; $i < $num_alternatives; $i++) {
        for ($j = $i + 1; $j < $num_alternatives; $j++) { // Avoid comparing an alternative to itself and avoid redundant comparisons
            $fst_key = $alternatives[$i];
            $scd_key = $alternatives[$j];

            $concordance_f_s =[];
            $concordance_s_f =[];
            $discordance_f_s =[];
            $discordance_s_f =[];

            foreach ($weights as $k => $w) { // Assuming $weights is an associative array with keys matching the data_dict keys
                $val_fst = $data_dict[$fst_key][$k];
                $val_scd = $data_dict[$scd_key][$k];

                $diff_f_s = ($val_scd - $val_fst) / $delta;
                $diff_s_f = ($val_fst - $val_scd) / $delta;

                $discordance_f_s = $diff_f_s;
                $discordance_s_f = $diff_s_f;

                if ($val_fst >= $val_scd) {
                    $concordance_f_s = $w;
                }
                if ($val_fst <= $val_scd) {
                    $concordance_s_f = $w;
                }
            }

            $discordance_matrix[$i][$j] = max($discordance_f_s);
            $discordance_matrix[$j][$i] = max($discordance_s_f);
            $concordance_matrix[$i][$j] = array_sum($concordance_f_s);
            $concordance_matrix[$j][$i] = array_sum($concordance_s_f);
        }
    }

    return [$concordance_matrix, $discordance_matrix];
}


function __get_delta($data_dict) {
    $max_diff = 0;
    foreach ($data_dict as $row) {
        foreach ($row as $val) {
            foreach ($data_dict as $other_row) {
                foreach($other_row as $other_val){
                    $diff = abs($val - $other_val);
                    $max_diff = max($max_diff, $diff);
                }

            }
        }
    }
    return $max_diff;
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
// ... (fungsi threshold, dominasi, agregat, dan alternatifDominan tidak perlu diubah) ...





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

// --- Tampilkan hasil (sesuaikan dengan kebutuhan) ---
echo "Matrix Normalisasi:\n";
print_r($matrix_ternormalisasi);

echo "\nMatrix Terbobot:\n";
print_r($matrix_terbobot);

echo "\nMatrix Himpunan concordance     discordance:\n";
// Panggil fungsi concordance_discordance
print_r(list($concordance_matrix, $discordance_matrix) = concordance_discordance($data_dict, $weights));

echo "\nMatrix Concordance:\n";
print_r($matrix_concordance);

echo "\nMatrix Discordance:\n";
print_r($matrix_discordance);

echo "\nThreshold Concordance: " . $threshold_concordance . "\n";
echo "Threshold Discordance: " . $threshold_discordance . "\n";

echo "\nMatrix Dominasi Concordance:\n";
print_r($matrix_dominasi_concordance);

echo "\nMatrix Dominasi Discordance:\n";
print_r($matrix_dominasi_discordance);

echo "\nMatrix Agregat:\n";
print_r($matrix_agregat);

echo "\nAlternatif Dominan: ";
print_r($alternatif_dominan);

?>