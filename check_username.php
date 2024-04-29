<?php
// check_username.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    include('config.php');
    
    $username = $_POST['username'];

    // Menggunakan prepared statement
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if(mysqli_num_rows($result) > 0) {
            $d = mysqli_fetch_object($result);
            $_SESSION['status_username'] = true;
            $_SESSION['user_global'] = $d;
            $_SESSION['id_user'] = $d->id_user;

            $response = array(
                'exists' => true,
                'username' => $d->username // Menambahkan username ke respons
            );
        } else {
            $response = array('exists' => false);
        }
    } else {
        // Handle error jika kueri tidak berhasil dijalankan
        $response = array('error' => 'Database query error');
    }

    echo json_encode($response);
}
?>
