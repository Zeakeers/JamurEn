<?php
 	session_start();
	$timeout = 1; // setting timeout dalam menit
	$logout = "login.php"; // redirect halaman logout

	$timeout = $timeout * 180; // menit ke detik
	if(isset($_SESSION['start_session'])){
		$elapsed_time = time()-$_SESSION['start_session'];
		if($elapsed_time >= $timeout){
			session_destroy();
			echo "<script type='text/javascript'>alert('Sesi telah berakhir');window.location='$logout'</script>";
		}
	}

	$_SESSION['start_session']=time();

	include('config.php');
	if($_SESSION['status_login'] != true){
		echo '<script>window.location="login.php"</script>';
	}

  $query = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '".$_SESSION['id_user']."' ");
	$d = mysqli_fetch_object($query);
?>


<script>
    setTimeout(function () {
        location.reload(); // Halaman akan diperbarui
    }, 12000); // 
</script>
<?php
include('config.php');

$query_realtime = "SELECT * FROM realtime_data ORDER BY waktu DESC LIMIT 1";
$result_realtime = mysqli_query($conn, $query_realtime);

if ($result_realtime) {
    $data_realtime = mysqli_fetch_assoc($result_realtime);
    $suhu_realtime = mysqli_real_escape_string($conn, $data_realtime['suhu']);
    $kelembapan_realtime = mysqli_real_escape_string($conn, $data_realtime['kelembapan']);

    // Tambahkan kondisi untuk memeriksa apakah suhu di atas 29.99 atau kelembapan di bawah 60
    if ($suhu_realtime > 29.99 || $kelembapan_realtime < 60) {
        // Cek apakah data sudah ada di tabel riwayat
        $query_check_existence = "SELECT * FROM riwayat WHERE jamursuhu_max = '$suhu_realtime' AND kelembapan = '$kelembapan_realtime'";
        $result_check_existence = mysqli_query($conn, $query_check_existence);

        if ($result_check_existence && mysqli_num_rows($result_check_existence) == 0) {
            // Masukkan data baru ke tabel riwayat
            $query_insert_suhu_kelembapan = "INSERT INTO riwayat (jamur_tanggal, jamursuhu_max, kelembapan) VALUES (NOW(), '$suhu_realtime', '$kelembapan_realtime')";
            $result_insert_suhu = mysqli_query($conn, $query_insert_suhu_kelembapan);

            if (!$result_insert_suhu) {
                echo "Error saat menyisipkan data: " . mysqli_error($conn);
            }

            // Hapus data lama sehingga hanya menyisakan 6 data terbaru
            $query_delete_old_data = "DELETE FROM riwayat WHERE jamur_id NOT IN (SELECT jamur_id FROM (SELECT jamur_id FROM riwayat ORDER BY jamur_tanggal DESC LIMIT 6) AS r)";
            $result_delete_old_data = mysqli_query($conn, $query_delete_old_data);

            if (!$result_delete_old_data) {
                echo "Error saat menghapus data lama: " . mysqli_error($conn);
            }
        } else {
            echo "Data dengan nilai yang sama sudah ada pada tabel riwayat.";
        }
    } else {
        echo "Nilai suhu dan kelembapan tidak memenuhi syarat untuk disimpan pada tabel riwayat.";
    }
} else {
    echo "Error saat mengambil data realtime: " . mysqli_error($conn);
}








if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Jalankan query untuk mengubah kategori rusak menjadi sehat
  $query = "UPDATE deteksi SET kategori = 'sehat' WHERE kategori = 'rusak'";
  $result = mysqli_query($conn, $query);

  if ($result) {
      // Berhasil diubah
      echo json_encode(array('status' => 'success', 'message' => 'Data berhasil diubah.'));
      header("location: dashboard.php");
      exit; // Penting: keluar dari skrip PHP setelah memberikan tanggapan
  } else {
      // Gagal diubah
      echo json_encode(array('status' => 'error', 'message' => 'Terjadi kesalahan. Data tidak dapat diubah.'));
      header("location: dashboard.php");
      exit; // Penting: keluar dari skrip PHP setelah memberikan tanggapan
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>DASHBOARD - JAMUR.EN</title>
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
  <link href="css/style.css" rel="stylesheet">
  <script src="dist/sweetalert2.all.min.js"></script>

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Sep 18 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style>
  .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 60px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      z-index: 1;
      /* Tambahkan properti border untuk menambahkan border berwarna hitam */
      border: 2px solid #000;
  }

  .close {
    /* Tambahkan properti absolute untuk memposisikan ikon close di bagian kanan atas */
    position: absolute;
    top: 10px;
    right: 10px;
    /* Tambahkan properti cursor pointer untuk menunjukkan bahwa ini dapat diklik */
    cursor: pointer;
    /* Tambahkan gaya lain sesuai keinginan Anda */
    font-size: 20px;
    color: #000; /* Warna ikon close */
  }

  .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 0;
  }
</style>


<body>
</head>

<body>
<!-- Pop-up Container -->
<div class="popup" id="popup">
    <!-- Tambahkan elemen span dengan kelas close untuk menampilkan ikon close -->
    <span class="close" onclick="closePopup()">&times;</span>
    <!-- Konten Pop-up -->
  
    <!-- Tambahkan properti style untuk membuat teks rata tengah -->
    <h3 style="text-align: center;">Ubah Data Baglog</h3>
    <!-- Tambahkan formulir atau elemen lain untuk mengubah data -->
    <!-- Tambahkan properti style untuk membuat teks rata tengah -->
    <p style="text-align: center;">klik ubah agar status baglog menjadi sehat</p>
  </br>
  <form action="dashboard.php" method="POST">

    <!-- Ganti tombol "Tutup" dengan ikon close -->
    <button onclick="ubahData()" style="display: block; margin: 0 auto; font-size: 18px; padding: 7px 20px; background-color: green; color: #fff; border: none; border-radius: 5px;">Ubah</button>
</form>
</div>

<!-- Overlay untuk menutup pop-up saat diklik di luar pop-up -->
<div class="overlay" id="overlay" onclick="closePopup()"></div>

 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">

<div class="d-flex align-items-center justify-content-between">
  <a href="dashboard.php" class="logo d-flex align-items-center">
    <img src="assets/img/logo.png" alt="">
    <span>JAMUR.EN</span>
  </a>
</div><!-- End Logo -->

<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">

    <li class="nav-item dropdown">

      <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span class="badge bg-primary badge-number">3</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
            <i class="bi bi-bell"></i>
              Notifikasi Monitoring Tanaman Jamur
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <script>
        
</script>
            <?php
                  $cek1 = mysqli_query($conn,"SELECT * FROM realtime_data ORDER BY waktu AND suhu DESC LIMIT 1");
                  while ($tampil = mysqli_fetch_assoc($cek1)){
                    $waktu = $tampil['waktu'];
                    $suhu = $tampil['suhu'];
                    $kelembapan = $tampil['kelembapan'];
                  ?>
                  <?php
                    if($suhu >= 30){
                      ?>
            <li class="notification-item">
              <i class="bi bi-thermometer-high text-danger card-icon"></i>
              <div>
              <p><?php echo date('H:i | d-m-Y', strtotime($waktu))
                    ?></p>
                      <h4> <?php echo date(($suhu))
                      ?>°C Suhu teralalu tinggi!</h4>
                  <h4>Sistem menyala</h4>

                  <?php
                    } elseif($suhu <= 30){
                      ?>
                      <li class="notification-item">
              <i class="bi bi-thermometer-low text-primary card-icon"></i>
              <div>
              <p><?php echo date('H:i | d-m-Y', strtotime($waktu))
                    ?></p>
                      <h4> <?php echo date(($suhu))
                      ?>°C Suhu Aman!</h4>
                  <h4>Sistem Mati</h4>
                  <?php
                    }else{                     
                    }
                    ?>
                
              </div>
            </li><?php } ?>           
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <?php
                    if($kelembapan <= 60){
                      ?>
            <li class="notification-item">
              <i class="bi bi-thermometer-sun text-danger"></i>
              <div>
                <p><?php echo date('H:i | d-m-Y', strtotime($waktu))
                    ?></p>
                <h4><?php echo date(($kelembapan))
                      ?>% Kelembapan terlalu tinggi!</h4>
                <h4>Sistem menyala</h4>
              </div>
              <?php
                    } elseif($kelembapan >= 0){
                      ?>
                      <li class="notification-item">
              <i class="bi bi-thermometer-snow text-primary"></i>
              <div>
                <p><?php echo date('H:i | d-m-Y', strtotime($waktu))
                    ?></p>
                <h4><?php echo date(($kelembapan))
                      ?>% Kelembapan terjaga!</h4>
                <h4>Sistem mati</h4>
              </div>
              
            </li>
            <?php } ?>

            <li>
              <hr class="dropdown-divider">
            </li>
                    
            <?php
$cek1 = mysqli_query($conn, "SELECT COUNT(*) AS jumlah_rusak FROM deteksi WHERE kategori = 'rusak'");
$tampil = mysqli_fetch_assoc($cek1);
$jumlah_rusak = $tampil['jumlah_rusak'];

if ($jumlah_rusak > 0) {
    // Jika ada data dengan kategori 'rusak'
    $query = "SELECT waktu_deteksi, kategori
              FROM deteksi
              WHERE kategori = 'rusak'
              ORDER BY waktu_deteksi DESC
              LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $tampil = mysqli_fetch_assoc($result);
        $waktu_deteksi = $tampil['waktu_deteksi'];
        $kategori = $tampil['kategori'];

        // Menampilkan jumlah kategori rusak
        $query_jumlah_rusak = "SELECT COUNT(*) AS jumlah_rusak FROM deteksi WHERE kategori = 'rusak'";
        $result_jumlah_rusak = mysqli_query($conn, $query_jumlah_rusak);
        $tampil_jumlah_rusak = mysqli_fetch_assoc($result_jumlah_rusak);
        $jumlah_rusak = $tampil_jumlah_rusak['jumlah_rusak'];

        ?>
 <!-- Teks "Ubah Sekarang" -->
 <li class="notification-item">
      <i class="bi bi-exclamation-circle text-danger"></i>
      <div>
        <p><?php echo date('H:i | d-m-Y', strtotime($waktu_deteksi)); ?></p>
        <h4><?php echo $jumlah_rusak; ?> Baglog terkena hama!</h4>
        <style>
    .btn-primary:hover {
        background-color: #28a745; /* Warna hijau sesuai keinginan Anda */
    }
</style>
        <!-- Tambahkan atribut onclick untuk memanggil fungsi openPopup() saat diklik -->
        <button onclick="openPopup()" class="btn btn-primary btn-sm">Ubah Status</button>
      </div>
    </li>


        <?php
    } else {
        // Handle jika query tidak mengembalikan hasil
        echo "Query tidak mengembalikan hasil.";
    }
} else {
    // Jika tidak ada data dengan kategori 'rusak'
    $query = "SELECT waktu_deteksi, kategori
              FROM deteksi
              ORDER BY waktu_deteksi DESC
              LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $tampil = mysqli_fetch_assoc($result);
        $waktu_deteksi = $tampil['waktu_deteksi'];
        $kategori = $tampil['kategori'];

        ?>
        <li class="notification-item">
            <i class="bi bi-check-circle text-success"></i>
            <div>
                <p><?php echo date('H:i | d-m-Y', strtotime($waktu_deteksi)); ?></p>
                <h4>Semua baglog sehat</h4>
                <h4>Selamat!</h4>
            </div>
        </li>
        <?php
    }
}
?>
          </ul><!-- End Notification Dropdown Items -->

    </li><!-- End Notification Nav -->

    <li class="nav-item">
      <a class="nav-link nav-icon" href="dashboard.php">
        <i class="bi bi-clock-history"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link nav-icon" id="logout" href="logout.php" >
      <i class="bi bi-arrow-up-right-square"></i>
      </a>
    </li>
    

    <!-- End Messages Nav -->
</nav><!-- End Icons Navigation -->

</header><!-- End Header -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Histori Penyiraman</h1>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h6 class="card-title">Rekap Penyiraman Jamur Harian</h6>

                 <!-- Table with hoverable rows -->
              <table class="table table-hover">
                <thead>
                  <tr style="text-align: center">
                    <th scope="col">No.</th>
                    <th scope="col">ID</th>
                    <th scope="col">Jam</th>
                    <th scope="col">Hari & Tanggal</th>
                    <th scope="col">Suhu Maksimum</th>
                    <th scope="col">Kelembapan</th>
                  </tr>
                </thead>
                <tbody>
                <?php
$no = 1;
$cek1 = mysqli_query($conn, "SELECT * FROM riwayat ORDER BY jamur_tanggal DESC LIMIT 5");
while ($tampil = mysqli_fetch_assoc($cek1)) {
    $jamur_id = $tampil['jamur_id'];
    $jamur_tanggal = $tampil['jamur_tanggal'];
    $suhumax = $tampil['jamursuhu_max'];
    $kelembapan = $tampil['kelembapan'];
?>
    <tr style="text-align: center">
        <th scope="row"><?php echo $no++ ?></th>
        <td><?php echo 'JE-' . sprintf('%03d', $jamur_id) ?></td>
        <td><?php echo date('H:i:s', strtotime($jamur_tanggal)) ?></td>
        <td><?php echo date('d F Y', strtotime($jamur_tanggal)) ?></td>
        <td><?php echo $suhumax ?> °C</td>
        <td><?php echo $kelembapan ?> %</td>
    </tr>
<?php } ?>
 <!-- -->
                </tbody>
              </table>
              <!-- End Table with hoverable rows -->

                </div>

              </div>
            </div><!-- End Recent Sales -->
          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
          <!-- Website Traffic -->
          <div class="card" style="height: 89%">
            <div class="card-body" 
            
            style="text-align: center">
              
              <h2 class="card-title">Suhu dan Kelembapan Terkini</h2>

            
                  </br></br></br>
                  <div style="font-size:35px">
                  <?php
                  $cek1 = mysqli_query($conn,"SELECT * FROM realtime_data ORDER BY waktu AND suhu DESC LIMIT 1");
                  while ($tampil = mysqli_fetch_assoc($cek1)){
                    $waktu = $tampil['waktu'];
                    $suhu = $tampil['suhu'];
                    $kelembapan = $tampil['kelembapan'];
                  ?>
                   <?php
                   if($suhu >= 30){
                   ?>
                   
              <i class="bi bi-thermometer-high card-icon text-danger" style="font-size: 35px"> </i><a class="text-danger"><?php echo $suhu
                    ?> °C</a>
                  <?php
                   }elseif($suhu <= 30){
                   ?>
              <i class="bi bi-thermometer-low card-icon text-primary" style="font-size: 35px"></i><a class="text-primary"><?php echo $suhu
                    ?> °C</a>
                    
                    <?php }} ?>

                  &nbsp;&nbsp;&nbsp;
                  
                   <?php
                   if($kelembapan <= 60){
                   ?>
                  <i class="bi bi-thermometer-sun text-danger card-icon" style="font-size: 35px;"></i>&nbsp;<a class="text-danger"><?php echo $kelembapan
                    ?> %</a>
                    <?php
                   }elseif($kelembapan >= 0){
                   ?>
              <i class="bi bi-thermometer-snow card-icon text-primary" style="font-size: 35px"></i><a class="text-primary"><?php echo $kelembapan
                    ?> °C</a>
<?php } ?>
            </div>
          </div><!-- End Website Traffic -->

 

        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <script>
  function openPopup() {
    document.getElementById("popup").style.display = "block";
    document.getElementById("overlay").style.display = "block";
  }

  function closePopup() {
    document.getElementById("popup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
  }

  function ubahData() {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', window.location.href, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var response = JSON.parse(xhr.responseText);

        alert(response.message);

        if (response.status === 'success') {
          closePopup();

          // Panggil fungsi untuk memperbarui notifikasi setelah data berhasil diubah
          updateNotificationItem();

        }
      } 
    }
  };

  xhr.send();
}


  function updateNotificationItem() {
  var notificationItem = document.querySelector('.notification-item');

  if (notificationItem) {
    notificationItem.innerHTML = `
      <i class="bi bi-check-circle text-success"></i>
      <div>
        <p>${getCurrentTime()}</p>
        <h4>Semua baglog sehat</h4>
        <h4>Selamat!</h4>
      </div>
    `;
    
  }          
}


</script>

  
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


  <script src="jquery.js"></script>
    <script>
      $(document).on('click', '#logout', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Klik keluar jika yakin!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Keluar',
          cancelButtonText: 'Batal'
          }).then((result) => {
          if (result.isConfirmed) {
            window.location ='logout.php';				
          }
        })
      })
    </script>

</body>

</html>