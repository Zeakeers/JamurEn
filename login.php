<?php
session_start();
include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
  <title>LOGIN - JAMUR.EN</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
<!-- Tambahkan skrip jQuery dan Bootstrap JS ini di bagian <head> -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="dist/sweetalert2.all.min.js"></script>
  

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.17.0/font/bootstrap-icons.css" rel="stylesheet">


  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 09 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">

              <img src="assets/img/logo.png" alt="" style="width: 300px; height: auto;">
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Masuk ke Akun Anda</h5>
                    <p class="text-center small">Masukkan Username dan Kata Sandi Anda!</p>
                  </div>

                  <form action="" method="POST" class="row g-3 needs-validation" novalidate>
  <div class="input-group has-validation">
    <div class="col-12">
      <label for="yourUsername" class="form-label">Username :</label>
      <input type="text" name="user" id="nama" class="form-control" autocomplete="off" required>
      <div class="invalid-feedback">Silahkan Masukkan Username!.</div>
    </div>
  </div>

  <div class="col-12">
    <label for="yourPassword" class="form-label">Kata Sandi :</label>
    <div class="input-group">
        <input type="password" name="pass" id="pass" class="form-control" autocomplete="off" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="bi bi-eye"></i>
        </button>
    </div>
    <div class="invalid-feedback">Silahkan Masukkan Kata Sandi!</div>
</div>


  <div class="col-12 d-flex justify-content-end"><!-- Menggunakan class justify-content-end di sini -->
    <!-- Tambahkan tautan ini di tempat Anda ingin menampilkan tautan "Lupa Password" -->
    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Lupa Password?</a>
  </div>

  <style>
    .btn-primary:hover {
        background-color: #28a745; /* Warna hijau sesuai keinginan Anda */
    }
</style>

<div class="col-12">
    <button class="btn btn-primary w-100" name="submit" type="submit">Masuk</button>
</div>
</form>

<?php
                    if(isset($_POST['submit'])){
                      
                      
                      $user = $_POST['user'];
                      $pass = $_POST['pass'];

                      $cek = mysqli_query($conn, "SELECT * FROM user WHERE username ='".$user."' AND password = '".MD5($pass)."'");
                      $cek3 = mysqli_query($conn, "SELECT * FROM user WHERE username ='".$user."'");
                      $cek4 = mysqli_query($conn, "SELECT * FROM user WHERE password ='".MD5($pass)."'");
                      if(mysqli_num_rows($cek) > 0){
                        $d = mysqli_fetch_object($cek);
                        $_SESSION['status_login'] = true;
                        $_SESSION['user_global'] = $d;
                        $_SESSION['id_user'] = $d->id_user;
                        ?>
                        <script>
                        Swal.fire({
                          title: 'Berhasil Masuk!',
                          text: 'Selamat Datang <?php echo $_SESSION['user_global']->username ?>!',
                          icon: 'success'
                        }).then((result) => {
                          window.location="dashboard.php";
                        })
                        </script>
                        <?php
                      }elseif (mysqli_num_rows($cek3) > 0){
                        $d = mysqli_fetch_object($cek);
                        ?>
                        <script>
                        Swal.fire({
                          icon: 'error',
                          title: 'Gagal...',
                          text: 'Kata Sandi anda salah!',
                          }).then((result) => {
                          window.location="login.php";
                        })
                        </script>
                        <?php
                      }elseif (mysqli_num_rows($cek4) > 0){
                        $d = mysqli_fetch_object($cek);
                        ?>
                        <script>
                        Swal.fire({
                          icon: 'error',
                          title: 'Gagal...',
                          text: 'Username anda salah!',
                          }).then((result) => {
                          window.location="login.php";
                        })
                        </script>
                        <?php
                      }else{
                        ?>
                        <script>
                        Swal.fire({
                          icon: 'error',
                          title: 'Gagal...',
                          text: 'Username dan Kata Sandi anda salah!',
                          }).then((result) => {
                          window.location="login.php";
                        })
                        </script>
                        <?php
                      }
                    }
                  ?>

                </div>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
<!-- Tambahkan modal ini di akhir tubuh HTML Anda -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Lupa Kata sandi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="usernameForm">
                    <div class="mb-3">
                        <label for="forgot_user" class="form-label">Masukkan Username Anda:</label>
                        <input type="text" name="forgot_user" id="forgot_user" class="form-control" required>
                    </div>
                    <div class="text-center">
                    <button type="button" class="btn btn-primary" name="submit2" onclick="checkUsername()">Cek Username</button>
                    </div>
                </form>

                <form action="" method="POST" id="passwordChangeForm" style="display: none;" onsubmit="return checkPasswordChange();">
    <!-- Tambahkan hidden input untuk menyimpan username -->
    <input type="hidden" readonly name="username" id="change_password_username">
                
    <div class="mb-3">
        <label for="new_password" class="form-label">Kata sandi Baru:</label>
        <div class="input-group">
            <input type="password" id="pass1" name="pass1" class="form-control" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Konfirmasi Kata sandi Baru:</label>
        <div class="input-group">
            <input type="password" id="pass2" name="pass2" class="form-control" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword2">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" name="change_password" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

            </div>
        </div>
    </div>
</div>

<script>
    function checkUsername() {
        var username = document.getElementById('forgot_user').value;

        // Gunakan AJAX untuk mengirim permintaan ke server
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);

                if (response.exists) {
                    // Username ditemukan di database
                    Swal.fire({
                        title: 'Username Terdeteksi!',
                        text: 'Silahkan ' + response.username + ' untuk melanjutkan membuat kata sandi baru!',
                        icon: 'success'
                    }).then((result) => {
                        if (result.value) {
                            // Menyimpan username ke input hidden
                            document.getElementById('change_password_username').value = response.username;

                            // Menampilkan form ganti password dan menyembunyikan form username
                            document.getElementById('usernameForm').style.display = 'none';
                            document.getElementById('passwordChangeForm').style.display = 'block';
                        }
                    });
                } else {
                    // Username tidak ada di database
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal...',
                        text: 'Username tidak terdaftar!',
                    });
                }
            }
        };

        xhr.open('POST', 'check_username.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('username=' + encodeURIComponent(username));
    }
</script>
<script>
    // Ganti nama fungsi menjadi checkPasswordChange
    function checkPasswordChange() {
        var pass1 = document.getElementById('pass1').value;
        var pass2 = document.getElementById('pass2').value;
        var username = document.getElementById('change_password_username').value;

        if (pass1 !== pass2) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal...',
                text: 'Konfirmasi Kata Sandi Baru Tidak Sesuai!',
            });
            return false; // Mencegah pengiriman formulir
        }

        // Menggunakan AJAX untuk mengirim permintaan reset password
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);

                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Kata Sandi Berhasil Diubah!',
                        icon: 'success'
                    }).then((result) => {
                        window.location = "login.php";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal...',
                        text: 'Gagal mengubah kata sandi!',
                    });
                }
            }
        };

        xhr.open('POST', 'reset_password.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('username=' + encodeURIComponent(username) + '&new_password=' + encodeURIComponent(pass1));

        return false; // Mencegah pengiriman formulir
    }
</script>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var passInput = document.getElementById('pass');
        var type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>

<script>
    document.getElementById('togglePassword1').addEventListener('click', function () {
        togglePasswordVisibility('pass1', this);
    });

    document.getElementById('togglePassword2').addEventListener('click', function () {
        togglePasswordVisibility('pass2', this);
    });

    function togglePasswordVisibility(passId, button) {
        var passInput = document.getElementById(passId);
        var type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passInput.setAttribute('type', type);
        button.querySelector('i').classList.toggle('bi-eye');
        button.querySelector('i').classList.toggle('bi-eye-slash');
    }
</script>


</html>
