<?php
$host = "localhost";
$user = "root";
$password = "";         // Default XAMPP password is empty
$database = "student_db";

$conn = mysqli_connect($host, $user, $password, $database, 3307);

if (!$conn) {
    die("<div style='color:red; font-family:sans-serif; padding:20px;'>
        ❌ Database Connection Failed: " . mysqli_connect_error() . "
        <br><br>Make sure:<br>
        1. XAMPP MySQL is running<br>
        2. You've created the database (see setup instructions)
    </div>");
}
?>
