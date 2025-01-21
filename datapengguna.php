<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Query untuk mengambil data pengguna
$sql = "SELECT * FROM pengguna";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPK Electre - Data Pengguna</title>
  <link rel="stylesheet" href="Assets/styles/datapengguna.css">
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
      width: 50%;
      /* Lebar modal */
      max-width: 500px;
      /* Lebar maksimum modal */
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

    /* CSS untuk form di dalam modal */
    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
    }

    .form-group input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
    }

    .btn-submit {
      background-color: #4CAF50;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">
        <i class="fas fa-database"></i>
        <span>SPK ELECTRE</span>
      </div>

      <div class="menu">
        <a href="dasboardadmin.php" class="menu-item">
          <i class="fas fa-chart-line"></i>
          Dashboard
        </a>
        <div class="menu-section">
          <span class="section-title">MASTER DATA</span>
          <a href="datakriteria.php" class="menu-item">Data Kriteria</a>
          <a href="dataalternatif.php" class="menu-item">Data Alternatif</a>
          <a href="datapenilaian.php" class="menu-item">Data Penilaian</a>
          <a href="dataperhitungan.php" class="menu-item">Data Perhitungan</a>
          <a href="datanilaiakhir.php" class="menu-item">Data Hasil Akhir</a>
        </div>
        <div class="menu-section">
          <span class="section-title">MASTER USER</span>
          <a href="#" class="menu-item active">Data Pengguna</a>
        </div>
        <a href="logout.php" class="menu-item">Logout</a>
      </div>

    </div>

    <div class="main-content">
      <div class="header">
        <div class="admin-section">
          <span>ADMIN</span>
          <i class="fas fa-user"></i>
        </div>
      </div>

      <div class="content">
        <h2>
          <i class="fas fa-users"></i>
          Data Pengguna
        </h2>

        <div class="data-controls">
          <button class="add-button" onclick="openModal()">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        </div>

        <div id="dataModal" class="modal">
          <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2>Tambah/Edit Data Pengguna</h2>
            <form id="dataForm">
              <input type="hidden" id="id">
              <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
                <small>Kosongkan jika tidak ingin mengubah password</small>
              </div>
              <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
              </div>
              <div class="form-group">
                <label for="level">Level:</label>
                <select id="level" name="level">
                  <option value="admin">Admin</option>
                  <option value="user">User</option>
                </select>
              </div>
              <button type="submit" class="btn-submit">Simpan</button>
            </form>
          </div>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Email</th>
                <th>Nama</th>
                <th>Level</th>
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
                  echo "<td>" . $row['email'] . "</td>";
                  echo "<td>" . $row['nama'] . "</td>";
                  echo "<td>" . $row['level'] . "</td>";
                  echo "<td>";
                  echo "<button class=\"btn-edit\" onclick=\"editData(" . $row['id'] . ")\"><i class=\"fas fa-edit\"></i></button>";
                  echo "<button class=\"btn-delete\" onclick=\"deleteData(" . $row['id'] . ")\"><i class=\"fas fa-trash\"></i></button>";
                  echo "</td>";
                  echo "</tr>";
                  $no++;
                }
              } else {
                echo "<tr><td colspan='5'>Tidak ada data pengguna.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
      document.getElementById("id").value = ""; // Kosongkan field id
    }

    // Fungsi untuk menutup modal
    function closeModal() {
      document.getElementById("dataModal").style.display = "none";
    }

    // Fungsi untuk edit data
    function editData(id) {
      // Ambil data pengguna dari server menggunakan AJAX
      var xhr = new XMLHttpRequest();
      xhr.open("GET", "get_pengguna.php?id=" + id, true);
      xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          var pengguna = JSON.parse(this.responseText);
          // Isi form dengan data yang diterima dari server
          document.getElementById("id").value = pengguna.id;
          document.getElementById("email").value = pengguna.email;
          document.getElementById("nama").value = pengguna.nama;
          document.getElementById("level").value = pengguna.level;
          // Buka modal setelah form terisi
          document.getElementById("dataModal").style.display = "block";
        }
      };
      xhr.send();
    }

    // Fungsi untuk delete data
    function deleteData(id) {
      if (confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
        // Kirim permintaan hapus ke server menggunakan AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_pengguna.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            location.reload();
          }
        };
        xhr.send("id=" + id);
      }
    }

    // Event listener untuk form submit (AJAX)
    document.getElementById("dataForm").addEventListener("submit", function (event) {
      event.preventDefault();

      // Ambil data dari form
      var id = document.getElementById("id").value;
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var nama = document.getElementById("nama").value;
      var level = document.getElementById("level").value;

      // Kirim data ke server menggunakan AJAX
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "simpan_pengguna.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {

        if (this.readyState == 4 && this.status == 200) {
          alert(this.responseText);
          closeModal();
          location.reload();
        }
      };
      xhr.send("id=" + id + "&email=" + email + "&password=" + password + "&nama=" + nama + "&level=" + level);
    });
  </script>
</body>

</html>