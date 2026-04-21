<?php
include 'db.php';

$id = $_POST['id'];
$name = $_POST['name'];

$sql = "UPDATE students SET name='$name' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Record updated!";
} else {
    echo "Error: " . $conn->error;
}
?>
