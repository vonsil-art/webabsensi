<?php
include '../config/koneksi.php';

$query = mysqli_query($koneksi, "SELECT * FROM tbl_guru ORDER BY id_guru DESC");
?>

<div class="crud-header">
    <h2>Data Guru</h2>
    <p>Pada halaman ini menampilkan data guru atau akun guru.</p>
</div>

<div class="crud-section">
    <h4>Tabel Guru :</h4>
    <a href="ad_index.php?page=tambahGuru" class="btn-small btn-add">
        <i class="bi bi-plus-lg"></i> Tambah Guru
    </a>

    <div class="table-wrapper">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>Username</th>
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
                        <td><?php echo htmlspecialchars($data['nip']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_guru']); ?></td>
                        <td><?php echo htmlspecialchars($data['username']); ?></td>
                        <td>
                            <a href="ad_index.php?page=ubahGuru&id=<?php echo $data['id_guru']; ?>" class="btn-small btn-edit">Edit</a>
                            <button type="button" class="btn-small btn-delete" onclick="konfirmasiHapus(<?php echo $data['id_guru']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="delete-overlay" id="deleteOverlay">
    <div class="delete-modal">
        <p>Hapus data guru ?</p>
        <div class="delete-action">
            <a href="#" id="btnYaHapus" class="btn-confirm-yes">Ya</a>
            <button type="button" class="btn-confirm-no" onclick="tutupKonfirmasi()">Tidak</button>
        </div>
    </div>
</div>

<script>
function konfirmasiHapus(idGuru) {
    const modal = document.getElementById("deleteOverlay");
    const tombolYa = document.getElementById("btnYaHapus");
    tombolYa.href = "ad_index.php?page=hapusGuru&id=" + idGuru;
    modal.style.display = "flex";
}

function tutupKonfirmasi() {
    document.getElementById("deleteOverlay").style.display = "none";
}
</script>