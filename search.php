<?php
include 'db.php';

$search = $_GET['search'];

$sql = "SELECT * FROM students WHERE name LIKE '%$search%'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo $row['id'] . " - " . $row['name'] . " - " . $row['email'];
    echo "<br>";
}
?>