<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id == 0) {
    header("Location: ad_index.php?page=kelas");
    exit;
}

$query = mysqli_query($koneksi, "DELETE FROM tbl_kelas WHERE id_kelas='$id'");

if ($query) {
    echo "<script>
            alert('Kelas berhasil dihapus!');
            window.location.href='ad_index.php?page=kelas';
          </script>";
} else {
    echo "<script>
            alert('Kelas gagal dihapus. Pastikan kelas tidak memiliki data siswa.');
            window.location.href='ad_index.php?page=kelas';
          </script>";
}
?>