<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id == 0) {
    header("Location: ad_index.php?page=guru");
    exit;
}

$query = mysqli_query($koneksi, "DELETE FROM tbl_guru WHERE id_guru='$id'");

if ($query) {
    echo "<script>
            alert('Guru berhasil dihapus!');
            window.location.href='ad_index.php?page=guru';
          </script>";
} else {
    echo "<script>
            alert('Guru gagal dihapus. Pastikan guru tidak sedang menjadi wali kelas.');
            window.location.href='ad_index.php?page=guru';
          </script>";
}
?>