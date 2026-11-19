<?php
include '../config/koneksi.php';

$guru = mysqli_query($koneksi, "SELECT * FROM tbl_guru ORDER BY nama_guru ASC");

if (isset($_POST['simpan'])) {
    $nama_kelas = trim($_POST['nama_kelas']);
    $id_wali_guru = $_POST['id_wali_guru'];

    $nama_kelas = mysqli_real_escape_string($koneksi, $nama_kelas);

    $cek_kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas WHERE nama_kelas='$nama_kelas'");

    if (mysqli_num_rows($cek_kelas) > 0) {
        echo "<script>alert('Kelas sudah terdaftar!');</script>";
    } else {
        if ($id_wali_guru == '') {
            $query = mysqli_query($koneksi, "INSERT INTO tbl_kelas (nama_kelas, id_wali_guru) VALUES ('$nama_kelas', NULL)");
        } else {
            $id_wali_guru = (int) $id_wali_guru;
            $query = mysqli_query($koneksi, "INSERT INTO tbl_kelas (nama_kelas, id_wali_guru) VALUES ('$nama_kelas', '$id_wali_guru')");
        }

        if ($query) {
            echo "<script>
                    alert('Data kelas berhasil ditambahkan!');
                    window.location.href='ad_index.php?page=kelas';
                  </script>";
        } else {
            echo "<script>alert('Gagal menambahkan data kelas!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Tambah Data Kelas</h2>
    <p>Silakan Masukkan Data Kelas.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>Nama Kelas :</label>
            <input type="text" name="nama_kelas" required>
        </div>
        <div class="crud-form-group">
            <label>Wali Kelas :</label>
            <select name="id_wali_guru">
                <option value="">-- Belum Ditentukan --</option>
                <?php while ($data_guru = mysqli_fetch_assoc($guru)) { ?>
                    <option value="<?php echo $data_guru['id_guru']; ?>">
                        <?php echo htmlspecialchars($data_guru['nama_guru']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="simpan" class="btn-small btn-save">Simpan</button>
            <a href="ad_index.php?page=kelas" class="btn-small btn-back">Kembali</a>
        </div>
    </form>
</div>