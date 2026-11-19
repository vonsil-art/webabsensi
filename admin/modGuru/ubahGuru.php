<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* AMBIL DATA GURU */
$get_data = mysqli_query($koneksi, "SELECT * FROM tbl_guru WHERE id_guru='$id'");
$data = mysqli_fetch_assoc($get_data);

/* CEK DATA GURU */
if (!$data) {
    echo "<script>
            alert('Data guru tidak ditemukan!');
            window.location.href='ad_index.php?page=guru';
          </script>";
    return;
}

/* PROSES UPDATE */
if (isset($_POST['update'])) {
    $nip = trim($_POST['nip']);
    $nama_guru = trim($_POST['nama_guru']);
    $username = trim($_POST['username']);
    $password_baru = $_POST['password'];

    $nip = mysqli_real_escape_string($koneksi, $nip);
    $nama_guru = mysqli_real_escape_string($koneksi, $nama_guru);
    $username = mysqli_real_escape_string($koneksi, $username);

    /* CEK USERNAME */
    $cek_username = mysqli_query($koneksi, "SELECT * FROM tbl_guru WHERE username='$username' AND id_guru!='$id'");
    /* CEK NIP */
    $cek_nip = mysqli_query($koneksi, "SELECT * FROM tbl_guru WHERE nip='$nip' AND id_guru!='$id'");

    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('Username sudah digunakan guru lain!');</script>";
    } elseif (mysqli_num_rows($cek_nip) > 0) {
        echo "<script>alert('NIP sudah digunakan guru lain!');</script>";
    } else {
        /* JIKA PASSWORD DIISI */
        if ($password_baru != '') {
            $password = password_hash($password_baru, PASSWORD_DEFAULT);
            $query = mysqli_query($koneksi, "UPDATE tbl_guru SET nip='$nip', nama_guru='$nama_guru', username='$username', password='$password' WHERE id_guru='$id'");
        } else {
            /* JIKA PASSWORD TIDAK DIUBAH */
            $query = mysqli_query($koneksi, "UPDATE tbl_guru SET nip='$nip', nama_guru='$nama_guru', username='$username' WHERE id_guru='$id'");
        }

        if ($query) {
            echo "<script>
                    alert('Data guru berhasil diubah!');
                    window.location.href='ad_index.php?page=guru';
                  </script>";
        } else {
            echo "<script>alert('Gagal mengubah data guru!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Edit Data Guru</h2>
    <p>Silakan Edit Data Guru.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>NIP :</label>
            <input type="text" name="nip" value="<?php echo htmlspecialchars($data['nip']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Nama Guru :</label>
            <input type="text" name="nama_guru" value="<?php echo htmlspecialchars($data['nama_guru']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Username :</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Password :</label>
            <div class="password-input">
                <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengganti password">
                <i class="bi bi-eye-slash" id="togglePassword" onclick="togglePassword()"></i>
            </div>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="update" class="btn-small btn-back">Update</button>
            <a href="ad_index.php?page=guru" class="btn-small btn-delete">Batal</a>
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