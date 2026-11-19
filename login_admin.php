
<?php
session_start();
include "config/koneksi.php";

if (isset($_POST['submit'])) {
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    if (empty($username) || empty($password)) {
        echo "<script>
                alert('Username dan password tidak boleh kosong!');
              </script>";
    } else {
        $username = mysqli_real_escape_string($koneksi, $username);

        $query = mysqli_query(
            $koneksi,
            "SELECT * FROM tbl_admin
             WHERE username='$username'"
        );

        if (mysqli_num_rows($query) == 1) {
            $data = mysqli_fetch_assoc($query);

            if (password_verify($password, $data['password'])) {
                $_SESSION['id_admin'] = $data['id_admin'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['is_admin'] = true;

                header("Location: admin/ad_index.php");
                exit;
            } else {
                echo "<script>
                        alert('Password salah!');
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Username tidak ditemukan!');
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login Admin</title>

    <link
        rel="stylesheet"
        href="css/login.css"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
</head>

<body>
<div class="login-container">
    <div class="login-form">
        <div class="logo-container">
            <img
                src="gambar/LogoDarulFatah.png"
                alt="LogoDarulFatah"
            >
        </div>

        <h1>Selamat Datang Admin</h1>

        <p class="description">
            Silakan masuk ke akun Admin untuk mengelola data sistem absensi.
        </p>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>

                <div class="input-box">
                    <i class="bi bi-person icon-left"></i>
                    <input
                        type="text"
                        name="user"
                        placeholder="Masukkan username"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <div class="password-title">
                    <label>Password</label>
                    <a href="#" class="forgot-password">
                        Lupa Password?
                    </a>
                </div>

                <div class="input-box">
                    <i class="bi bi-key icon-left"></i>
                    <input
                        type="password"
                        name="pass"
                        id="password"
                        placeholder="Masukkan password"
                        required
                    >
                    <i
                        class="bi bi-eye icon-right"
                        id="togglePassword"
                        onclick="togglePassword()"
                    ></i>
                </div>
            </div>

            <button
                type="submit"
                name="submit"
                class="btn-login"
            >
                Masuk
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("togglePassword");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        password.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>
</body>
</html>
