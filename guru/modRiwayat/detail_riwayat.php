<?php
date_default_timezone_set('Asia/Jakarta');

$id_kelas = isset($_GET['id_kelas']) ? (int) $_GET['id_kelas'] : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

if ($id_kelas == 0 || $tanggal == '') {
    echo "<script>
            alert('Data riwayat tidak ditemukan!');
            window.location.href='gr_index.php?page=riwayat';
          </script>";
    return;
}
$tanggal = mysqli_real_escape_string($koneksi, $tanggal);

$query_kelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas WHERE id_kelas='$id_kelas'");
$kelas = mysqli_fetch_assoc($query_kelas);
if (!$kelas) {
    echo "<script>
            alert('Data kelas tidak ditemukan!');
            window.location.href='gr_index.php?page=riwayat';
          </script>";
    return;
}
$nama_kelas = $kelas['nama_kelas'];
$tanggal_tampil = date('d-m-Y', strtotime($tanggal));

$query_siswa = mysqli_query($koneksi, "SELECT
                                        tbl_siswa.id_siswa,
                                        tbl_siswa.nis,
                                        tbl_siswa.nama_siswa,
                                        tbl_absensi.status
                                       FROM tbl_absensi
                                       JOIN tbl_siswa
                                       ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
                                       WHERE tbl_siswa.id_kelas='$id_kelas'
                                       AND tbl_absensi.tanggal='$tanggal'
                                       ORDER BY tbl_siswa.nama_siswa ASC");
$dataSiswa = array();
while ($siswa = mysqli_fetch_assoc($query_siswa)) {
    $dataSiswa[] = $siswa;
}
$totalSiswa = count($dataSiswa);

if (isset($_POST['simpan_perubahan'])) {
    $status_baru = isset($_POST['detail_status']) ? $_POST['detail_status'] : array();
    if (count($status_baru) < $totalSiswa) {
        echo "<script>
                alert('Masih ada status siswa yang belum dipilih!');
              </script>";
    } else {
        foreach ($dataSiswa as $siswa) {
            $id_siswa = $siswa['id_siswa'];
            $status = $status_baru[$id_siswa];
            if (
                $status == "Hadir" ||
                $status == "Sakit" ||
                $status == "Izin" ||
                $status == "Alpa"
            ) {
                mysqli_query($koneksi, "UPDATE tbl_absensi
                                        SET status='$status'
                                        WHERE id_siswa='$id_siswa'
                                        AND tanggal='$tanggal'");
            }
        }
        echo "<script>
                alert('Perubahan absensi berhasil disimpan!');
                window.location.href='gr_index.php?page=detail_riwayat&id_kelas=$id_kelas&tanggal=$tanggal';
              </script>";
        return;
    }
}

$jumlah_hadir = 0;
$jumlah_sakit = 0;
$jumlah_izin = 0;
$jumlah_alpa = 0;
foreach ($dataSiswa as $siswa) {
    if ($siswa['status'] == "Hadir") {
        $jumlah_hadir++;
    }
    if ($siswa['status'] == "Sakit") {
        $jumlah_sakit++;
    }
    if ($siswa['status'] == "Izin") {
        $jumlah_izin++;
    }
    if ($siswa['status'] == "Alpa") {
        $jumlah_alpa++;
    }
}

if ($totalSiswa > 0) {
    $persen_hadir = ($jumlah_hadir / $totalSiswa) * 100;
    $persen_sakit = ($jumlah_sakit / $totalSiswa) * 100;
    $persen_izin = ($jumlah_izin / $totalSiswa) * 100;
    $persen_alpa = ($jumlah_alpa / $totalSiswa) * 100;
} else {
    $persen_hadir = 0;
    $persen_sakit = 0;
    $persen_izin = 0;
    $persen_alpa = 0;
}
?>
<div class="detail-heading">
    <div>
        <a href="gr_index.php?page=riwayat" class="back-riwayat">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
        <h1>
            Detail Riwayat Absensi
        </h1>
        <p>
            Lihat detail kehadiran siswa pada tanggal yang dipilih.
        </p>
    </div>
    <div class="detail-heading-right">
        <div class="date-box">
            <i class="bi bi-calendar3"></i>
            <?php echo $tanggal_tampil; ?>
        </div>
        <div class="class-box">
            Kelas <?php echo htmlspecialchars($nama_kelas); ?>
        </div>
    </div>
</div>

<div class="stat-container">

    <div class="stat-card">
        <div class="stat-title">
            Hadir
            <span class="dot hadir"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <span id="detailHadir" class="jumlah-status">
                    <?php echo $jumlah_hadir; ?>
                </span>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-hadir" id="persenHadir">
                <?php
                echo number_format($persen_hadir,1,',','.');
                ?>%
            </div>
        </div>
        <p>Hadir di kelas</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Sakit
            <span class="dot sakit"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <span id="detailSakit" class="jumlah-status">
                    <?php echo $jumlah_sakit; ?>
                </span>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-sakit" id="persenSakit">
                <?php
                echo number_format($persen_sakit,1,',','.');
                ?>%
            </div>
        </div>
        <p>Surat keterangan sakit</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Izin
            <span class="dot izin"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <span id="detailIzin" class="jumlah-status">
                    <?php echo $jumlah_izin; ?>
                </span>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-izin" id="persenIzin">
                <?php
                echo number_format($persen_izin,1,',','.');
                ?>%
            </div>
        </div>
        <p>Izin orang tua</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Alpa
            <span class="dot alpa"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <span id="detailAlpa" class="jumlah-status">
                    <?php echo $jumlah_alpa; ?>
                </span>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-alpa" id="persenAlpa">
                <?php
                echo number_format($persen_alpa,1,',','.'
                );
                ?>%
            </div>
        </div>
        <p>Belum ada kabar</p>
    </div>
</div>
<form method="POST" action="gr_index.php?page=detail_riwayat&id_kelas=<?php echo $id_kelas; ?>&tanggal=<?php echo $tanggal; ?>" onsubmit="return konfirmasiSimpan();">

    <div class="daftar-header">
        <h3>
            Daftar Siswa
        </h3>
        <?php if ($totalSiswa > 0) { ?>
            <div class="detail-buttons">
                <button type="button" class="btn-edit-riwayat" id="btnEdit" onclick="editAbsensi()">
                    <i class="bi bi-pencil-square"></i>
                    Edit Absensi
                </button>
                <button type="button" class="btn-batal-edit" id="btnBatalEdit" onclick="batalEdit()" style="display: none;">
                    Batal
                </button>
                <button type="submit" name="simpan_perubahan" class="btn-simpan-perubahan" id="btnSimpanEdit" style="display: none;">
                    <i class="bi bi-check-circle"></i>
                    Simpan Perubahan
                </button>
            </div>
        <?php } ?>
    </div>

    <div class="student-list">
        <?php
        $no = 1;
        foreach ($dataSiswa as $siswa) {
            $id_siswa = $siswa['id_siswa'];
            $status = $siswa['status'];
        ?>
            <div class="student-card <?php if ($no > 20) echo 'student-hidden'; ?>">
                <div class="student-info">
                    <strong>
                        <?php echo htmlspecialchars($siswa['nama_siswa']); ?>
                    </strong>
                    <span>
                        NIS:
                        <?php echo htmlspecialchars($siswa['nis']); ?>
                        •
                        Kelas
                        <?php echo htmlspecialchars($nama_kelas); ?>
                    </span>
                </div>
                <div class="status-options">

                    <input type="radio" name="detail_status[<?php echo $id_siswa; ?>]" id="detailHadir<?php echo $id_siswa; ?>" value="Hadir" class="status-radio detail-radio" <?php if ($status == "Hadir") echo "checked"; ?> disabled>
                    <label for="detailHadir<?php echo $id_siswa; ?>" class="status-label status-hadir">
                        <span></span>
                        Hadir
                    </label>
                    <input type="radio" name="detail_status[<?php echo $id_siswa; ?>]" id="detailSakit<?php echo $id_siswa; ?>" value="Sakit" class="status-radio detail-radio" <?php if ($status == "Sakit") echo "checked"; ?> disabled>
                    <label for="detailSakit<?php echo $id_siswa; ?>" class="status-label status-sakit">
                        <span></span>
                        Sakit
                    </label>

                    <input type="radio" name="detail_status[<?php echo $id_siswa; ?>]" id="detailIzin<?php echo $id_siswa; ?>" value="Izin" class="status-radio detail-radio" <?php if ($status == "Izin") echo "checked"; ?> disabled>
                    <label for="detailIzin<?php echo $id_siswa; ?>" class="status-label status-izin">
                        <span></span>
                        Izin
                    </label>

                    <input type="radio" name="detail_status[<?php echo $id_siswa; ?>]" id="detailAlpa<?php echo $id_siswa; ?>" value="Alpa" class="status-radio detail-radio" <?php if ($status == "Alpa") echo "checked"; ?> disabled>
                    <label for="detailAlpa<?php echo $id_siswa; ?>" class="status-label status-alpa">
                        <span></span>
                        Alpa
                    </label>
                </div>
            </div>
        <?php
            $no++;
        }
        ?>
        <?php if ($totalSiswa == 0) { ?>
            <div class="student-card">
                <div class="student-info">
                    <strong>
                        Tidak ada data absensi
                    </strong>
                    <span>
                        Belum ada data absensi untuk kelas dan tanggal ini.
                    </span>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if ($totalSiswa > 20) { ?>
        <div class="load-more-container">
            <button type="button" class="btn-load-more" id="btnLoadMoreDetail" onclick="tampilkanLebihBanyakDetail()">
                <i class="bi bi-chevron-down"></i>
                Tampilkan Lebih Banyak
            </button>
            <span id="jumlahDitampilkanDetail">
                Menampilkan 20 dari
                <?php echo $totalSiswa; ?>
                siswa
            </span>
        </div>
    <?php } ?>
</form>
<script>
let dataAwal = [];

document.querySelectorAll(".detail-radio:checked").forEach(function(status) {
    dataAwal.push({
        name: status.name,
        value: status.value
    });
});

document.querySelectorAll(".detail-radio").forEach(function(status) {
    status.addEventListener("change", function() {
        hitungDetail();
    });
});

function editAbsensi() {
    const semuaRadio =
        document.querySelectorAll(".detail-radio");
    semuaRadio.forEach(function(radio) {
        radio.disabled = false;
    });
    document.getElementById("btnEdit").style.display =
        "none";
    document.getElementById("btnBatalEdit").style.display =
        "inline-flex";
    document.getElementById("btnSimpanEdit").style.display =
        "inline-flex";
}

function batalEdit() {
    dataAwal.forEach(function(data) {
        const radio =
            document.querySelector(
                'input[name="' +
                data.name +
                '"][value="' +
                data.value +
                '"]'
            );
        if (radio) {
            radio.checked = true;
        }
    });
    document.querySelectorAll(".detail-radio").forEach(function(radio) {
        radio.disabled = true;
    });
    hitungDetail();
    document.getElementById("btnEdit").style.display =
        "inline-flex";
    document.getElementById("btnBatalEdit").style.display =
        "none";
    document.getElementById("btnSimpanEdit").style.display =
        "none";
}

function hitungDetail() {
    let hadir = 0;
    let sakit = 0;
    let izin = 0;
    let alpa = 0;
    const dipilih =
        document.querySelectorAll(".detail-radio:checked");
    dipilih.forEach(function(status) {
        if (status.value === "Hadir") {
            hadir++;
        }
        if (status.value === "Sakit") {
            sakit++;
        }
        if (status.value === "Izin") {
            izin++;
        }
        if (status.value === "Alpa") {
            alpa++;
        }
    });
    const total =
        <?php echo $totalSiswa; ?>;
    document.getElementById("detailHadir").innerHTML =
        hadir;
    document.getElementById("detailSakit").innerHTML =
        sakit;
    document.getElementById("detailIzin").innerHTML =
        izin;
    document.getElementById("detailAlpa").innerHTML =
        alpa;
    if (total > 0) {
        document.getElementById("persenHadir").innerHTML =
            ((hadir / total) * 100)
            .toFixed(1)
            .replace(".", ",") + "%";
        document.getElementById("persenSakit").innerHTML =
            ((sakit / total) * 100)
            .toFixed(1)
            .replace(".", ",") + "%";
        document.getElementById("persenIzin").innerHTML =
            ((izin / total) * 100)
            .toFixed(1)
            .replace(".", ",") + "%";
        document.getElementById("persenAlpa").innerHTML =
            ((alpa / total) * 100)
            .toFixed(1)
            .replace(".", ",") + "%";
    }
}

function konfirmasiSimpan() {
    const yakin = confirm(
        "Simpan perubahan data absensi?"
    );
    if (!yakin) {
        return false;
    }
    return true;
}

function tampilkanLebihBanyakDetail() {
    const siswaTersembunyi =
        document.querySelectorAll(".student-hidden");
    siswaTersembunyi.forEach(function(siswa) {
        siswa.classList.remove("student-hidden");
    });
    document.getElementById("btnLoadMoreDetail").style.display =
        "none";
    document.getElementById("jumlahDitampilkanDetail").innerHTML =
        "Menampilkan <?php echo $totalSiswa; ?> dari <?php echo $totalSiswa; ?> siswa";
}
</script>
