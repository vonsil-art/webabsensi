<?php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* AMBIL DATA SISWA */
$get_data = mysqli_query($koneksi, "SELECT * FROM tbl_siswa WHERE id_siswa='$id'");
$data = mysqli_fetch_assoc($get_data);

/* CEK DATA SISWA */
if (!$data) {
    echo "<script>
            alert('Data siswa tidak ditemukan!');
            window.location.href='ad_index.php?page=siswa';
          </script>";
    return;
}

/* AMBIL DATA KELAS */
$kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas ORDER BY nama_kelas ASC");

/* PROSES UPDATE */
if (isset($_POST['update'])) {
    $nis = trim($_POST['nis']);
    $nama_siswa = trim($_POST['nama_siswa']);
    $id_kelas = (int) $_POST['id_kelas'];

    $nis = mysqli_real_escape_string($koneksi, $nis);
    $nama_siswa = mysqli_real_escape_string($koneksi, $nama_siswa);

    /* CEK NIS */
    $cek_nis = mysqli_query($koneksi, "SELECT * FROM tbl_siswa WHERE nis='$nis' AND id_siswa!='$id'");

    if (mysqli_num_rows($cek_nis) > 0) {
        echo "<script>alert('NIS sudah digunakan siswa lain!');</script>";
    } else {
        $query = mysqli_query($koneksi, "UPDATE tbl_siswa SET nis='$nis', nama_siswa='$nama_siswa', id_kelas='$id_kelas' WHERE id_siswa='$id'");

        if ($query) {
            echo "<script>
                    alert('Data siswa berhasil diubah!');
                    window.location.href='ad_index.php?page=siswa';
                  </script>";
        } else {
            echo "<script>alert('Gagal mengubah data siswa!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Edit Data Siswa</h2>
    <p>Silakan Edit Data Siswa.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>NIS :</label>
            <input type="text" name="nis" value="<?php echo htmlspecialchars($data['nis']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Nama Siswa :</label>
            <input type="text" name="nama_siswa" value="<?php echo htmlspecialchars($data['nama_siswa']); ?>" required>
        </div>
        <div class="crud-form-group">
            <label>Kelas :</label>
            <select name="id_kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <?php while ($data_kelas = mysqli_fetch_assoc($kelas)) { ?>
                    <option value="<?php echo $data_kelas['id_kelas']; ?>" <?php if ($data_kelas['id_kelas'] == $data['id_kelas']) { echo "selected"; } ?>>
                        <?php echo htmlspecialchars($data_kelas['nama_kelas']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="update" class="btn-small btn-back">Update</button>
            <a href="ad_index.php?page=siswa" class="btn-small btn-delete">Batal</a>
        </div>
    </form>
</div>