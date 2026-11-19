<?php
include '../config/koneksi.php';

// Mengambil data kelas yang sudah tersedia
$query_kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas ORDER BY nama_kelas ASC");

// Mengambil data siswa
$query = mysqli_query($koneksi, "SELECT tbl_siswa.*, tbl_kelas.nama_kelas FROM tbl_siswa JOIN tbl_kelas ON tbl_siswa.id_kelas = tbl_kelas.id_kelas ORDER BY tbl_siswa.id_siswa DESC");
?>

<div class="crud-header">
    <h2>Data Siswa</h2>
    <p>Pada halaman ini menampilkan data siswa.</p>
</div>

<div class="crud-section">
    <h4>Tabel Siswa :</h4>

    <!-- FILTER SISWA -->
    <div class="filter-siswa-container">
        <div class="filter-siswa-item">
            <label for="filterKelasSiswa">Filter Kelas</label>
            <select id="filterKelasSiswa">
                <option value="semua">Semua Kelas</option>
                <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                    <option value="<?php echo $kelas['id_kelas']; ?>"><?php echo htmlspecialchars($kelas['nama_kelas']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-siswa-item">
            <label for="filterTahunSiswa">Tahun Ajaran</label>
            <select id="filterTahunSiswa">
                <option value="semua">Semua Tahun</option>
                <option value="2026/2027">2026/2027</option>
            </select>
        </div>

        <button type="button" class="btn-reset-filter" onclick="resetFilterSiswa()">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </button>
    </div>

    <!-- TOMBOL TAMBAH -->
    <div class="aksi-header-siswa">
        <a href="ad_index.php?page=imporSiswa" class="btn-small btn-import-siswa">
            <i class="bi bi-file-earmark-excel"></i> Impor Excel
        </a>
        <a href="ad_index.php?page=tambahSiswa" class="btn-small btn-add">
            <i class="bi bi-plus-lg"></i> Tambah Siswa
        </a>
    </div>

    <!-- TABEL SISWA -->
    <div class="table-wrapper">
        <table class="crud-table" id="tabelDataSiswa">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr class="baris-siswa" data-kelas="<?php echo $data['id_kelas']; ?>" data-tahun="2026/2027">
                        <td class="nomor-siswa"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nis']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_siswa']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_kelas']); ?></td>
                        <td>
                            <a href="ad_index.php?page=ubahSiswa&id=<?php echo $data['id_siswa']; ?>" class="btn-small btn-edit">Edit</a>
                            <button type="button" class="btn-small btn-delete" onclick="konfirmasiHapusSiswa(<?php echo $data['id_siswa']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php } ?>
                <tr id="dataSiswaKosong" style="display: none;">
                    <td colspan="5" style="text-align: center; padding: 20px;">Data siswa tidak ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="jumlah-data-siswa" id="jumlahDataSiswa"></div>

    <!-- TOMBOL TAMPILKAN LEBIH BANYAK -->
    <div class="tampilkan-lebih-wrapper" style="text-align: center; margin-top: 15px;">
        <button type="button" id="btnTampilkanLebih" class="btn-small btn-add" style="display: none;" onclick="tampilkanLebihBanyakSiswa()">
            <i class="bi bi-chevron-down"></i> Tampilkan Lebih Banyak
        </button>
    </div>
</div>

<!-- MODAL HAPUS -->
<div class="delete-overlay" id="deleteOverlaySiswa">
    <div class="delete-modal">
        <p>Hapus data siswa?</p>
        <div class="delete-action">
            <a href="#" id="btnYaHapusSiswa" class="btn-confirm-yes">Ya</a>
            <button type="button" class="btn-confirm-no" onclick="tutupKonfirmasiSiswa()">Tidak</button>
        </div>
    </div>
</div>

<script>
// PENGATURAN TAMPILAN BERTAHAP
const JUMLAH_PER_HALAMAN = 20;
let batasTampilSiswa = JUMLAH_PER_HALAMAN;

// FILTER KELAS DAN TAHUN AJARAN + BATAS TAMPIL
function filterDataSiswa() {
    const filterKelas = document.getElementById("filterKelasSiswa").value;
    const filterTahun = document.getElementById("filterTahunSiswa").value;
    const semuaBaris = document.querySelectorAll(".baris-siswa");
    const dataKosong = document.getElementById("dataSiswaKosong");
    const jumlahData = document.getElementById("jumlahDataSiswa");
    const tombolLebih = document.getElementById("btnTampilkanLebih");

    let jumlahCocok = 0;
    let jumlahTampil = 0;

    semuaBaris.forEach(function(baris) {
        const kelas = baris.dataset.kelas;
        const tahun = baris.dataset.tahun;
        const cocokKelas = filterKelas === "semua" || kelas === filterKelas;
        const cocokTahun = filterTahun === "semua" || tahun === filterTahun;

        if (cocokKelas && cocokTahun) {
            jumlahCocok++;
            if (jumlahCocok <= batasTampilSiswa) {
                baris.style.display = "";
                jumlahTampil++;
                baris.querySelector(".nomor-siswa").textContent = jumlahCocok;
            } else {
                baris.style.display = "none";
            }
        } else {
            baris.style.display = "none";
        }
    });

    // Jika data tidak ditemukan
    dataKosong.style.display = (jumlahCocok === 0) ? "" : "none";

    // Tombol hanya muncul jika masih ada data tersisa
    tombolLebih.style.display = (jumlahCocok > batasTampilSiswa) ? "inline-block" : "none";

    // Menampilkan jumlah data
    jumlahData.textContent = "Menampilkan " + jumlahTampil + " dari " + jumlahCocok + " siswa";
}

// TAMPILKAN 20 SISWA BERIKUTNYA
function tampilkanLebihBanyakSiswa() {
    batasTampilSiswa += JUMLAH_PER_HALAMAN;
    filterDataSiswa();
}

// EVENT FILTER
function ubahFilterSiswa() {
    batasTampilSiswa = JUMLAH_PER_HALAMAN;
    filterDataSiswa();
}

document.getElementById("filterKelasSiswa").addEventListener("change", ubahFilterSiswa);
document.getElementById("filterTahunSiswa").addEventListener("change", ubahFilterSiswa);

// RESET FILTER
function resetFilterSiswa() {
    document.getElementById("filterKelasSiswa").value = "semua";
    document.getElementById("filterTahunSiswa").value = "semua";
    batasTampilSiswa = JUMLAH_PER_HALAMAN;
    filterDataSiswa();
}

// KONFIRMASI HAPUS SISWA
function konfirmasiHapusSiswa(idSiswa) {
    const modal = document.getElementById("deleteOverlaySiswa");
    const tombolYa = document.getElementById("btnYaHapusSiswa");
    tombolYa.href = "ad_index.php?page=hapusSiswa&id=" + idSiswa;
    modal.style.display = "flex";
}

function tutupKonfirmasiSiswa() {
    document.getElementById("deleteOverlaySiswa").style.display = "none";
}

// TAMPILKAN JUMLAH AWAL
filterDataSiswa();
</script>