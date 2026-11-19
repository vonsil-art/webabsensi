<?php
session_start();
include "config/koneksi.php";

if (isset($_POST['submit'])) {

    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    if (empty($username) || empty($password)) {

        echo "<script>
                alert('Username dan password tidak boleh kosong!');
              </script>";

    } else {

        $username = mysqli_real_escape_string($koneksi, $username);

        $query = mysqli_query($koneksi, "
            SELECT * FROM tbl_guru
            WHERE username='$username'
        ");

        if (mysqli_num_rows($query) == 1) {

            $data = mysqli_fetch_assoc($query);

            if (password_verify($password, $data['password'])) {

                $id_guru = $data['id_guru'];

                // Cek apakah guru merupakan wali kelas
                $cek_wali = mysqli_query($koneksi, "
                    SELECT * FROM tbl_kelas
                    WHERE id_wali_guru='$id_guru'
                    LIMIT 1
                ");

                if (mysqli_num_rows($cek_wali) > 0) {

                    // Login khusus wali kelas
                    $_SESSION['role'] = "wali";
                    $_SESSION['id_guru'] = $data['id_guru'];
                    $_SESSION['nama_guru'] = $data['nama_guru'];
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['is_login'] = true;

                    header("Location: guru/gr_index.php");
                    exit;

                } else {

                    // Guru biasa belum diperbolehkan login
                    echo "<script>
                            alert('Maaf, login guru biasa sementara dinonaktifkan. Hanya wali kelas yang dapat login!');
                          </script>";
                }

            } else {

                echo "<script>
                        alert('Password salah!');
                      </script>";

            }

        } else {

            echo "<script>
                    alert('Username tidak ditemukan!');
                  </script>";

        }
    }
}

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        $file = 'us_login.php';
        break;
    default:
        $file = 'us_login.php';
        break;
}

require 'user/us_header.php';
require $file;
require 'user/us_footer.php';
?>
