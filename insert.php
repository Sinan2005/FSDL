<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];

// Validation
if (empty($name) || empty($email) || empty($mobile)) {
    echo "All fields are required!";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format!";
    exit();
}

// Insert Query
$sql = "INSERT INTO students (name, email, mobile)
        VALUES ('$name', '$email', '$mobile')";

if ($conn->query($sql) === TRUE) {
    echo "Record inserted successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>