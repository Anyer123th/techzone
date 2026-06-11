<?php
// Conexión simple (proyecto escolar)
$conn = new mysqli("localhost", "root", "", "techzone");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
