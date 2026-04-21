<?php
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM students WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Record deleted!";
} else {
    echo "Error: " . $conn->error;
}
?>