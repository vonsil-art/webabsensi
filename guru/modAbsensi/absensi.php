<?php
date_default_timezone_set('Asia/Jakarta');
$tanggal_sekarang = date('d-m-Y');
$tanggal_database = date('Y-m-d');
/* AMBIL ID KELAS DARI URL */
/* AMBIL ID WALI KELAS DARI SESSION */
$id_guru = isset($_SESSION['id_guru']) ? (int) $_SESSION['id_guru'] : 0;

/* AMBIL KELAS SESUAI WALI KELAS YANG LOGIN */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT id_kelas, nama_kelas
     FROM tbl_kelas
     WHERE id_wali_guru='$id_guru'
     LIMIT 1"
);

$kelas = mysqli_fetch_assoc($query_kelas);

/* CEK APAKAH AKUN WALI KELAS SUDAH MEMILIKI KELAS */
if ($id_guru == 0 || !$kelas) {
    echo "<script>
            alert('Akun Anda belum memiliki kelas sebagai wali kelas!');
            window.location.href='gr_index.php';
          </script>";
    return;
}

$id_kelas = (int) $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];
/* AMBIL DATA SISWA */
$query_siswa = mysqli_query($koneksi, "SELECT * FROM tbl_siswa WHERE id_kelas='$id_kelas' ORDER BY nama_siswa ASC");
$dataSiswa = array();
while ($siswa = mysqli_fetch_assoc($query_siswa)) {
    $dataSiswa[] = $siswa;
}
$totalSiswa = count($dataSiswa);
/* PROSES SIMPAN ABSENSI */
if (isset($_POST['simpan_absensi'])) {
    $status_absensi = isset($_POST['status']) ? $_POST['status'] : array();
    $id_guru = $_SESSION['id_guru'];
    /* CEK APAKAH KELAS SUDAH DIABSEN HARI INI */
    $cek_absensi = mysqli_query($koneksi, "SELECT tbl_absensi.id_absensi
                                           FROM tbl_absensi
                                           JOIN tbl_siswa
                                           ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
                                           WHERE tbl_siswa.id_kelas='$id_kelas'
                                           AND tbl_absensi.tanggal='$tanggal_database'
                                           LIMIT 1");
    if (mysqli_num_rows($cek_absensi) > 0) {
        echo "<script>
                alert('Kelas ini sudah melakukan absensi hari ini!');
                window.location.href='gr_index.php';
              </script>";
        return;
    }
    /* CEK SEMUA SISWA SUDAH DIPILIH */
    if (count($status_absensi) < $totalSiswa) {
        echo "<script>
                alert('Masih ada siswa yang belum dipilih status absensinya!');
              </script>";
    } else {
        /* SIMPAN ABSENSI */
        foreach ($dataSiswa as $siswa) {
            $id_siswa = $siswa['id_siswa'];
            $status = $status_absensi[$id_siswa];
            mysqli_query($koneksi, "INSERT INTO tbl_absensi
                                    (id_siswa, id_guru, tanggal, status)
                                    VALUES
                                    ('$id_siswa', '$id_guru', '$tanggal_database', '$status')");
        }
        echo "<script>
                alert('Absensi berhasil disimpan!');
                window.location.href='gr_index.php';
              </script>";
        return;
    }
}
?>
<div class="absensi-heading">
    <div>
        <h1>Pengisian Absensi</h1>
        <p>
            <?php echo $totalSiswa; ?> siswa Kelas
            <?php echo htmlspecialchars($nama_kelas); ?>
            perlu dicatat absensi pagi hari ini.
        </p>
    </div>
    <div class="absensi-heading-right">
        <div class="date-box">
            <i class="bi bi-calendar3"></i>
            <?php echo $tanggal_sekarang; ?>
        </div>
        <div class="class-box">
            Kelas <?php echo htmlspecialchars($nama_kelas); ?>
        </div>
    </div>
</div>
<!-- RINGKASAN ABSENSI -->
<div class="stat-container">
    <!-- HADIR -->
    <div class="stat-card">
        <div class="stat-title">
            Hadir
            <span class="dot hadir"></span>
        </div>
        <div class="stat-number">
            <span id="jumlahHadir" class="jumlah-status">
                0
            </span>
            <span>Siswa</span>
        </div>
        <p>Hadir di kelas</p>
    </div>
    <!-- SAKIT -->
    <div class="stat-card">
        <div class="stat-title">
            Sakit
            <span class="dot sakit"></span>
        </div>
        <div class="stat-number">
            <span id="jumlahSakit" class="jumlah-status">
                0
            </span>
            <span>Siswa</span>
        </div>
        <p>Surat keterangan sakit</p>
    </div>
    <!-- IZIN -->
    <div class="stat-card">
        <div class="stat-title">
            Izin
            <span class="dot izin"></span>
        </div>
        <div class="stat-number">
            <span id="jumlahIzin" class="jumlah-status">
                0
            </span>
            <span>Siswa</span>
        </div>
        <p>Izin orang tua</p>
    </div>
    <!-- ALPA -->
    <div class="stat-card">
        <div class="stat-title">
            Alpa
            <span class="dot alpa"></span>
        </div>
        <div class="stat-number">
            <span id="jumlahAlpa" class="jumlah-status">
                0
            </span>
            <span>Siswa</span>
        </div>
        <p>Belum ada kabar</p>
    </div>
</div>
<!-- DAFTAR SISWA -->
<div class="daftar-header">
    <h3>Daftar Siswa</h3>
    <span>
        Pilih status absensi untuk setiap siswa
    </span>
</div>
<form
    id="formAbsensi"
    method="POST"
    action="gr_index.php?page=absensi&id_kelas=<?php echo $id_kelas; ?>"
    onsubmit="return validasiAbsensi();"
>
    <div class="student-list">
        <?php
        $no = 1;
        foreach ($dataSiswa as $siswa) {
            $id_siswa = $siswa['id_siswa'];
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
                        Kelas <?php echo htmlspecialchars($nama_kelas); ?>
                    </span>
                </div>
                <div class="status-options">
                    <!-- HADIR -->
                    <input
                        type="radio"
                        name="status[<?php echo $id_siswa; ?>]"
                        id="hadir<?php echo $id_siswa; ?>"
                        value="Hadir"
                        class="status-radio"
                    >
                    <label
                        for="hadir<?php echo $id_siswa; ?>"
                        class="status-label status-hadir"
                    >
                        <span></span>
                        Hadir
                    </label>
                    <!-- SAKIT -->
                    <input
                        type="radio"
                        name="status[<?php echo $id_siswa; ?>]"
                        id="sakit<?php echo $id_siswa; ?>"
                        value="Sakit"
                        class="status-radio"
                    >
                    <label
                        for="sakit<?php echo $id_siswa; ?>"
                        class="status-label status-sakit"
                    >
                        <span></span>
                        Sakit
                    </label>
                    <!-- IZIN -->
                    <input
                        type="radio"
                        name="status[<?php echo $id_siswa; ?>]"
                        id="izin<?php echo $id_siswa; ?>"
                        value="Izin"
                        class="status-radio"
                    >
                    <label
                        for="izin<?php echo $id_siswa; ?>"
                        class="status-label status-izin"
                    >
                        <span></span>
                        Izin
                    </label>
                    <!-- ALPA -->
                    <input
                        type="radio"
                        name="status[<?php echo $id_siswa; ?>]"
                        id="alpa<?php echo $id_siswa; ?>"
                        value="Alpa"
                        class="status-radio"
                    >
                    <label
                        for="alpa<?php echo $id_siswa; ?>"
                        class="status-label status-alpa"
                    >
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
                        Belum ada siswa
                    </strong>
                    <span>
                        Belum ada data siswa pada kelas ini.
                    </span>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- TAMPILKAN LEBIH BANYAK -->
    <?php if ($totalSiswa > 20) { ?>
        <div class="load-more-container">
            <button
                type="button"
                class="btn-load-more"
                id="btnLoadMore"
                onclick="tampilkanLebihBanyak()"
            >
                <i class="bi bi-chevron-down"></i>
                Tampilkan Lebih Banyak
            </button>
            <span id="jumlahDitampilkan">
                Menampilkan 20 dari
                <?php echo $totalSiswa; ?>
                siswa
            </span>
        </div>
    <?php } ?>
    <!-- TOMBOL -->
    <?php if ($totalSiswa > 0) { ?>
        <div class="absensi-action">
            <button
                type="button"
                class="btn-batal"
                onclick="batalAbsensi()"
            >
                <i class="bi bi-x-circle"></i>
                Batal
            </button>
            <button
                type="submit"
                name="simpan_absensi"
                class="btn-simpan-absensi"
            >
                <i class="bi bi-check-circle"></i>
                Simpan Absensi
            </button>
        </div>
    <?php } ?>
</form>
<script>
const semuaStatus =
    document.querySelectorAll(".status-radio");
semuaStatus.forEach(function(status) {
    status.addEventListener("change", function() {
        hitungAbsensi();
    });
});
function hitungAbsensi() {
    let hadir = 0;
    let sakit = 0;
    let izin = 0;
    let alpa = 0;
    const statusDipilih =
        document.querySelectorAll(".status-radio:checked");
    statusDipilih.forEach(function(status) {
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
    document.getElementById("jumlahHadir").innerHTML = hadir;
    document.getElementById("jumlahSakit").innerHTML = sakit;
    document.getElementById("jumlahIzin").innerHTML = izin;
    document.getElementById("jumlahAlpa").innerHTML = alpa;
}
function validasiAbsensi() {
    const jumlahSiswa =
        <?php echo $totalSiswa; ?>;
    const sudahDiisi =
        document.querySelectorAll(".status-radio:checked").length;
    if (sudahDiisi < jumlahSiswa) {
        const belumDiisi =
            jumlahSiswa - sudahDiisi;
        alert(
            "Masih ada " +
            belumDiisi +
            " siswa yang belum dipilih status absensinya."
        );
        return false;
    }
    const yakin = confirm(
        "Apakah data absensi sudah benar dan ingin disimpan?"
    );
    if (!yakin) {
        return false;
    }
    return true;
}
function tampilkanLebihBanyak() {
    const siswaTersembunyi =
        document.querySelectorAll(".student-hidden");
    siswaTersembunyi.forEach(function(siswa) {
        siswa.classList.remove("student-hidden");
    });
    const tombol =
        document.getElementById("btnLoadMore");
    const jumlah =
        document.getElementById("jumlahDitampilkan");
    tombol.style.display = "none";
    jumlah.innerHTML =
        "Menampilkan <?php echo $totalSiswa; ?> dari <?php echo $totalSiswa; ?> siswa";
}
function batalAbsensi() {
    const yakin = confirm(
        "Batalkan pengisian absensi dan kembali ke Dashboard?"
    );
    if (yakin) {
        window.location.href = "gr_index.php";
    }
}
</script>
