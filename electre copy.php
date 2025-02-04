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