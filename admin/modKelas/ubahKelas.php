<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* AMBIL DATA KELAS */
$get_data = mysqli_query($koneksi, "SELECT * FROM tbl_kelas WHERE id_kelas='$id'");
$data = mysqli_fetch_assoc($get_data);

/* CEK DATA KELAS */
if (!$data) {
    echo "<script>
            alert('Data kelas tidak ditemukan!');
            window.location.href='ad_index.php?page=kelas';
          </script>";
    return;
}

/* AMBIL DATA GURU */
$guru = mysqli_query($koneksi, "SELECT * FROM tbl_guru ORDER BY nama_guru ASC");

/* PROSES UPDATE */
if (isset($_POST['update'])) {
    $nama_kelas = trim($_POST['nama_kelas']);
    $id_wali_guru = $_POST['id_wali_guru'];

    $nama_kelas = mysqli_real_escape_string($koneksi, $nama_kelas);

    /* CEK NAMA KELAS */
    $cek_kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas WHERE nama_kelas='$nama_kelas' AND id_kelas!='$id'");

    if (mysqli_num_rows($cek_kelas) > 0) {
        echo "<script>alert('Nama kelas sudah digunakan!');</script>";
    } else {
        /* JIKA WALI KELAS BELUM DITENTUKAN */
        if ($id_wali_guru == '') {
            $query = mysqli_query($koneksi, "UPDATE tbl_kelas SET nama_kelas='$nama_kelas', id_wali_guru=NULL WHERE id_kelas='$id'");
        } else {
            /* JIKA ADA WALI KELAS */
            $id_wali_guru = (int) $id_wali_guru;
            $query = mysqli_query($koneksi, "UPDATE tbl_kelas SET nama_kelas='$nama_kelas', id_wali_guru='$id_wali_guru' WHERE id_kelas='$id'");
        }

        if ($query) {
            echo "<script>
                    alert('Data kelas berhasil diubah!');
                    window.location.href='ad_index.php?page=kelas';
                  </script>";
        } else {
            echo "<script>alert('Gagal mengubah data kelas!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Edit Data Kelas</h2>
    <p>Silakan Edit Data Kelas.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>Nama Kelas :</label>
            <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($data['nama_kelas']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Wali Kelas :</label>
            <select name="id_wali_guru">
                <option value="">-- Belum Ditentukan --</option>
                <?php while ($data_guru = mysqli_fetch_assoc($guru)) { ?>
                    <option value="<?php echo $data_guru['id_guru']; ?>" <?php if ($data_guru['id_guru'] == $data['id_wali_guru']) { echo "selected"; } ?>>
                        <?php echo htmlspecialchars($data_guru['nama_guru']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="update" class="btn-small btn-back">Update</button>
            <a href="ad_index.php?page=kelas" class="btn-small btn-delete">Batal</a>
        </div>
    </form>
</div>