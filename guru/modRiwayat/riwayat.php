<?php
date_default_timezone_set('Asia/Jakarta');
$tanggal_sekarang = date('d-m-Y');

$id_guru = $_SESSION['id_guru'];

$query_kelas = mysqli_query($koneksi,"SELECT DISTINCT tbl_kelas.id_kelas, 
                                                    tbl_kelas.nama_kelas
                                                    FROM tbl_absensi
                                                    JOIN tbl_siswa
                                                    ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
                                                    JOIN tbl_kelas
                                                    ON tbl_siswa.id_kelas = tbl_kelas.id_kelas
                                                    WHERE tbl_absensi.id_guru='$id_guru'
                                                    ORDER BY tbl_kelas.nama_kelas ASC"
);

$query_riwayat = mysqli_query($koneksi,"SELECT tbl_kelas.id_kelas,
                                            tbl_kelas.nama_kelas,
                                            tbl_absensi.tanggal,
                                            COUNT(*) AS total,
                                            SUM(tbl_absensi.status = 'Hadir') AS hadir,
                                            SUM(tbl_absensi.status = 'Sakit') AS sakit,
                                            SUM(tbl_absensi.status = 'Izin') AS izin,
                                            SUM(tbl_absensi.status = 'Alpa') AS alpa
                                            FROM tbl_absensi
                                            JOIN tbl_siswa
                                            ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
                                            JOIN tbl_kelas
                                            ON tbl_siswa.id_kelas = tbl_kelas.id_kelas
                                            WHERE tbl_absensi.id_guru='$id_guru'
                                            GROUP BY
                                                tbl_kelas.id_kelas,
                                                tbl_kelas.nama_kelas,
                                                tbl_absensi.tanggal
                                            ORDER BY
                                                tbl_absensi.tanggal DESC,
                                                tbl_kelas.nama_kelas ASC"
);
$riwayat = array();
while ($data = mysqli_fetch_assoc($query_riwayat)) {
    $riwayat[] = $data;
}
$total_riwayat = count($riwayat);
?>
<div class="riwayat-heading">
    <div>
        <h1>Riwayat Absensi</h1>
        <p>
            Menampilkan riwayat absensi yang Anda lakukan.
        </p>
    </div>
</div>
<div class="riwayat-filter">
    <div class="filter-title">
        Daftar Kelas
    </div>
    <div class="filter-right">
        <div class="riwayat-date">
            <?php echo $tanggal_sekarang; ?>
            <i class="bi bi-calendar3"></i>
        </div>
        <select id="filterKelas" class="filter-kelas">
            <option value="semua">
                Semua Kelas
            </option>
            <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                <option value="<?php echo $kelas['id_kelas']; ?>">
                    Kelas
                    <?php echo htmlspecialchars($kelas['nama_kelas']); ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>
<div class="riwayat-list">
    <?php foreach ($riwayat as $data) { ?>
        <?php
        $total = $data['total'];
        if ($total > 0) {
            $persen_hadir =
                ($data['hadir'] / $total) * 100;
            $persen_sakit =
                ($data['sakit'] / $total) * 100;
            $persen_izin =
                ($data['izin'] / $total) * 100;
            $persen_alpa =
                ($data['alpa'] / $total) * 100;
        } else {
            $persen_hadir = 0;
            $persen_sakit = 0;
            $persen_izin = 0;
            $persen_alpa = 0;
        }
        $tanggal_tampil =
            date('d-m-Y',strtotime($data['tanggal']));
        ?>
        <a href="gr_index.php?page=detail_riwayat&id_kelas=<?php echo $data['id_kelas']; ?>&tanggal=<?php echo $data['tanggal']; ?>" class="riwayat-card" data-kelas="<?php echo $data['id_kelas']; ?>">
            <div class="riwayat-info">
                <strong>
                    Kelas
                    <?php echo htmlspecialchars($data['nama_kelas']); ?>
                </strong>
                <span>
                    <?php echo $tanggal_tampil; ?>
                </span>
            </div>
            <div class="riwayat-status">
                <div class="status-history history-hadir">
                    <span></span>
                    Hadir
                    <?php
                    echo number_format($persen_hadir,1,',','.');
                    ?>%
                </div>
                <div class="status-history history-sakit">
                    <span></span>
                    Sakit
                    <?php
                    echo number_format($persen_sakit,1,',','.');
                    ?>%
                </div>
                <div class="status-history history-izin">
                    <span></span>
                    Izin
                    <?php
                    echo number_format($persen_izin,1,',','.');
                    ?>%
                </div>
                <div class="status-history history-alpa">
                    <span></span>
                    Alpa
                    <?php
                    echo number_format($persen_alpa,1,',','.');
                    ?>%
                </div>
            </div>
        </a>
    <?php } ?>
</div>
<div id="riwayatKosong" class="riwayat-kosong" <?php if ($total_riwayat == 0) { ?> style="display: block;" <?php } ?>>
    Belum ada riwayat absensi yang Anda lakukan.
</div>
<script>
const filterKelas =
    document.getElementById("filterKelas");
const riwayatCard =
    document.querySelectorAll(".riwayat-card");
const riwayatKosong =
    document.getElementById("riwayatKosong");
filterKelas.addEventListener("change", function() {
    const kelasDipilih =
        filterKelas.value;
    let jumlahTampil = 0;
    riwayatCard.forEach(function(card) {
        const kelas =
            card.getAttribute("data-kelas");
        if (
            kelasDipilih === "semua" ||
            kelas === kelasDipilih
        ) {
            card.style.display = "flex";
            jumlahTampil++;
        } else {
            card.style.display = "none";
        }
    });
    if (jumlahTampil === 0) {
        riwayatKosong.style.display = "block";
    } else {
        riwayatKosong.style.display = "none";
    }
});
</script>
