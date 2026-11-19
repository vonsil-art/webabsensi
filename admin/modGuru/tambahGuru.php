<?php
include '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $nip = trim($_POST['nip']);
    $nama_guru = trim($_POST['nama_guru']);
    $username = trim($_POST['username']);
    $password_asli = $_POST['password'];

    $nip = mysqli_real_escape_string($koneksi, $nip);
    $nama_guru = mysqli_real_escape_string($koneksi, $nama_guru);
    $username = mysqli_real_escape_string($koneksi, $username);

    $cek_username = mysqli_query($koneksi, "SELECT * FROM tbl_guru WHERE username='$username'");
    $cek_nip = mysqli_query($koneksi, "SELECT * FROM tbl_guru WHERE nip='$nip'");

    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } elseif (mysqli_num_rows($cek_nip) > 0) {
        echo "<script>alert('NIP sudah terdaftar!');</script>";
    } else {
        $password = password_hash($password_asli, PASSWORD_DEFAULT);
        $query = mysqli_query($koneksi, "INSERT INTO tbl_guru (nip, nama_guru, username, password) VALUES ('$nip', '$nama_guru', '$username', '$password')");

        if ($query) {
            echo "<script>
                    alert('Guru berhasil ditambahkan!');
                    window.location.href='ad_index.php?page=guru';
                  </script>";
        } else {
            echo "<script>alert('Gagal menambahkan guru!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Tambah Data Guru</h2>
    <p>Silakan Masukkan Data Guru.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>NIP :</label>
            <input type="text" name="nip" required>
        </div>
        <div class="crud-form-group">
            <label>Nama Guru :</label>
            <input type="text" name="nama_guru" required>
        </div>
        <div class="crud-form-group">
            <label>Username :</label>
            <input type="text" name="username" required>
        </div>
        <div class="crud-form-group">
            <label>Password :</label>
            <div class="password-input">
                <input type="password" name="password" id="password" required>
                <i class="bi bi-eye-slash" id="togglePassword" onclick="togglePassword()"></i>
            </div>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="simpan" class="btn-small btn-save">Simpan</button>
            <a href="ad_index.php?page=guru" class="btn-small btn-back">Kembali</a>
        </div>
    </form>
</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("togglePassword");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    } else {
        password.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    }
}
</script>