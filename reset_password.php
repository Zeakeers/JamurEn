<?php
session_start();
include('config.php');

// Ambil data dari POST
$username = $_POST['username'];
$new_password = $_POST['new_password'];

// Enkripsi kata sandi baru (sesuai dengan cara Anda melakukan hash sebelumnya)
$hashed_password = md5($new_password);

// Update kata sandi di database
$query = "UPDATE user SET password = '$hashed_password' WHERE username = '$username'";
$result = $conn->query($query);

// Beri respons ke JavaScript
if ($result) {
    $response = array("success" => true);
} else {
    $response = array("success" => false);
}

// Mengembalikan respons dalam format JSON
echo json_encode($response);

// Tutup koneksi
$conn->close();
?>
