<?php

var_dump($_POST);

$target_dir = "captured_images/"; // folder untuk menyimpan gambar
$target_filename = "photo.jpg"; // set the constant filename without an extension
$target_file = $target_dir . $target_filename;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
$file_name = pathinfo($_FILES["imageFile"]["name"], PATHINFO_BASENAME);

// Debugging: Print entire POST data
var_dump($_POST);

// ... (your existing code)

if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
        echo "Photo berhasil dipuload di server dengan nama " . $file_name;

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "jamur";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Ensure temperature data is properly received
        $temperature = isset($_POST["suhu"]) ? mysqli_real_escape_string($conn, $_POST["suhu"]) : "";
        $humidity = isset($_POST["kelembapan"]) ? mysqli_real_escape_string($conn, $_POST["kelembapan"]) : "";

        // Delete existing data from the table
        $deleteQuery = "DELETE FROM realtime_data";
        if ($conn->query($deleteQuery) === TRUE) {
            echo "Existing data deleted successfully.";
        } else {
            echo "Error deleting existing data: " . $conn->error;
        }

        // Debugging: Print SQL query
        $sql = "INSERT INTO realtime_data (suhu, kelembapan) VALUES ('$temperature', '$humidity')";
        echo "SQL Query: " . $sql;

        // Insert data into the database using a prepared statement
        $stmt = $conn->prepare("INSERT INTO realtime_data (suhu, kelembapan) VALUES (?, ?)");
        $stmt->bind_param("ss", $temperature, $humidity);

        if ($stmt->execute()) {
            echo "Data berhasil dimasukkan ke database. Temperature: " . $temperature . "Kelembapan: " . $humidity;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Sorry, Ada error dalam proses upload photo.";
    }
}
?>