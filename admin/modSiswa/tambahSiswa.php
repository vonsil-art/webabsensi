<?php
include '../config/koneksi.php';

$kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas ORDER BY nama_kelas ASC");

if (isset($_POST['simpan'])) {
    $nis = trim($_POST['nis']);
    $nama_siswa = trim($_POST['nama_siswa']);
    $id_kelas = (int) $_POST['id_kelas'];

    $nis = mysqli_real_escape_string($koneksi, $nis);
    $nama_siswa = mysqli_real_escape_string($koneksi, $nama_siswa);

    $cek_nis = mysqli_query($koneksi, "SELECT * FROM tbl_siswa WHERE nis='$nis'");

    if (mysqli_num_rows($cek_nis) > 0) {
        echo "<script>alert('NIS sudah terdaftar!');</script>";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tbl_siswa (nis, nama_siswa, id_kelas) VALUES ('$nis', '$nama_siswa', '$id_kelas')");

        if ($query) {
            echo "<script>
                    alert('Siswa berhasil ditambahkan!');
                    window.location.href='ad_index.php?page=siswa';
                  </script>";
        } else {
            echo "<script>alert('Gagal menambahkan siswa!');</script>";
        }
    }
}
?>

<div class="crud-header">
    <h2>Tambah Data Siswa</h2>
    <p>Silakan Masukkan Data Siswa.</p>
</div>

<div class="crud-form-card">
    <form method="POST" action="">
        <div class="crud-form-group">
            <label>NIS :</label>
            <input type="text" name="nis" required>
        </div>
        <div class="crud-form-group">
            <label>Nama Siswa :</label>
            <input type="text" name="nama_siswa" required>
        </div>
        <div class="crud-form-group">
            <label>Kelas :</label>
            <select name="id_kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <?php while ($data_kelas = mysqli_fetch_assoc($kelas)) { ?>
                    <option value="<?php echo $data_kelas['id_kelas']; ?>">
                        <?php echo htmlspecialchars($data_kelas['nama_kelas']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="crud-form-action">
            <button type="submit" name="simpan" class="btn-small btn-save">Simpan</button>
            <a href="ad_index.php?page=siswa" class="btn-small btn-back">Kembali</a>
        </div>
    </form>
</div>