<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="Assets/styles/dasboardadmin.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      SPK ELECTRE
    </div>
    
    
        <ul class="menu">
          
            <?php 
            session_start();
            $role = $_SESSION['level'];
            $nama= $_SESSION['nama'];
            if ($role == 'admin') {?>
                <li><a href="dasboardadmin.php">Dashboard</a></li>
                <li><a href="dataalternatif.php">Data Alternatif</a></li>
                <li><a href="datakriteria.php">Data Kriteria</a></li>
                <li><a href="datapenilaian.php">Data Penilaian</a></li>
                <li>  <a href="dataperhitungan.php">Data Perhitungan</a></li>
                <li><a href="datanilaiakhir.php">Data Nilai Akhir</a></li>
                <li><a href="datapengguna.php">Data Pengguna</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php } else if ($role == 'user') {?>
              <li><a href="dasboardadmin.php">Dashboard</a></li>
                <li><a href="datapenilaianuser.php">Data Penilaian</a></li>
                <li><a href="datanilaiakhiruser.php">Data Nilai Akhir</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php }?>
        </ul>


  </div>

  <div class="main-content">
    <header>
      <div class="welcome-message">
        <h1>Dashboard</h1>
        <p>Selamat datang <strong><?php echo $nama   ?></strong> Anda bisa mengoperasikan sistem dengan wewenang tertentu melalui pilihan menu di bawah.</p>
      </div>
    </header>
    <?php if ($role == 'admin') {?>
      <div class="card-container">
        <a href="datakriteria.php" class="card">Data Kriteria</a>
        <a href="dataalternatif.php" class="card">Data Alternatif</a>
        <a href="datapenilaian.php" class="card">Data Penilaian</a>
        <a href="dataperhitungan.php" class="card">Data Perhitungan</a>
        <a href="datanilaiakhir.php" class="card">Data Hasil Akhir</a>
        <a href="datapengguna.php" class="card">Data Pengguna</a>
          </div>
      <?php } else if ($role == 'user') {?>
        <div class="card-container">
        
        <a href="datapenilaian.php" class="card">Data Penilaian</a>
        
        <a href="datanilaiakhir.php" class="card">Data Hasil Akhir</a>
        
      </div>
      <?php }?>
  </div>
</body>
</html>