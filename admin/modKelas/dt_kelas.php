<?php
include '../config/koneksi.php';

$query = mysqli_query($koneksi, "SELECT tbl_kelas.*, tbl_guru.nama_guru FROM tbl_kelas LEFT JOIN tbl_guru ON tbl_kelas.id_wali_guru = tbl_guru.id_guru ORDER BY tbl_kelas.id_kelas DESC");
?>

<div class="crud-header">
    <h2>Data Kelas</h2>
    <p>Pada halaman ini menampilkan data kelas dan wali kelas.</p>
</div>

<div class="crud-section">
    <h4>Tabel Kelas :</h4>
    <a href="ad_index.php?page=tambahKelas" class="btn-small btn-add">
        <i class="bi bi-plus-lg"></i> Tambah Kelas
    </a>

    <div class="table-wrapper">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nama_kelas']); ?></td>
                        <td>
                            <?php
                            if ($data['nama_guru']) {
                                echo htmlspecialchars($data['nama_guru']);
                            } else {
                                echo "Belum Ditentukan";
                            }
                            ?>
                        </td>
                        <td>
                            <a href="ad_index.php?page=ubahKelas&id=<?php echo $data['id_kelas']; ?>" class="btn-small btn-edit">Edit</a>
                            <button type="button" class="btn-small btn-delete" onclick="konfirmasiHapusKelas(<?php echo $data['id_kelas']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="delete-overlay" id="deleteOverlayKelas">
    <div class="delete-modal">
        <p>Hapus data kelas ?</p>
        <div class="delete-action">
            <a href="#" id="btnYaHapusKelas" class="btn-confirm-yes">Ya</a>
            <button type="button" class="btn-confirm-no" onclick="tutupKonfirmasiKelas()">Tidak</button>
        </div>
    </div>
</div>

<script>
function konfirmasiHapusKelas(idKelas) {
    const modal = document.getElementById("deleteOverlayKelas");
    const tombolYa = document.getElementById("btnYaHapusKelas");
    tombolYa.href = "ad_index.php?page=hapusKelas&id=" + idKelas;
    modal.style.display = "flex";
}

function tutupKonfirmasiKelas() {
    document.getElementById("deleteOverlayKelas").style.display = "none";
}
</script>