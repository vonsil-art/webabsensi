<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id == 0) {
    header("Location: ad_index.php?page=siswa");
    exit;
}

$query = mysqli_query($koneksi, "DELETE FROM tbl_siswa WHERE id_siswa='$id'");

if ($query) {
    echo "<script>
            alert('Siswa berhasil dihapus!');
            window.location.href='ad_index.php?page=siswa';
          </script>";
} else {
    echo "<script>
            alert('Siswa gagal dihapus. Siswa mungkin sudah memiliki data absensi.');
            window.location.href='ad_index.php?page=siswa';
          </script>";
}
?>