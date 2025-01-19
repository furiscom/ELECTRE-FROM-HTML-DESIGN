<?php
session_start();

$conn = new mysqli("localhost", "root", "", "electre");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // Pastikan password tidak di-hash di sini

    $sql = "SELECT * FROM pengguna WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $row['level'];
        header("Location: dasboardadmin.php");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title> 
    <link rel="stylesheet" href="Assets/styles/login.css">
</head>
<body>
    <div class="login-container"> 
        <h2>Login Account</h2> 
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p> 
        <?php endif; ?>
        <form action="index.php" method="post"> 
            <input type="email" id="email" name="email" placeholder="admin@gmail.com" required> 
            <input type="password" id="password" name="password" placeholder="Password" required> 
            <button type="submit" id="loginButton"> 
                <span>🔒</span> Masuk 
            </button>
        </form>
    </div>
</body>
</html>