<?php
session_start();
include "conexion.php";
if(!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$uid = (int)$_SESSION["id"];
$pid = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($pid <= 0) {
    die("Producto inválido. <a href='tienda.php'>Volver</a>");
}

// verificar producto
$stmt = $conn->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();

if (!$prod) {
    die("Ese producto no existe. <a href='tienda.php'>Volver</a>");
}

$stmt2 = $conn->prepare("INSERT INTO compras(usuario_id, producto_id) VALUES(?, ?)");
$stmt2->bind_param("ii", $uid, $pid);
$stmt2->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Compra - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="tienda.php">Tech Zone</a>
  <a href="tienda.php">Volver a tienda</a>
</header>

<div class="container">
  <div class="card">
    <h2>✅ Compra registrada</h2>
    <p>Compraste: <strong><?php echo htmlspecialchars($prod["nombre"]); ?></strong></p>
    <p>Monto: <strong>$<?php echo number_format((float)$prod["precio"], 2); ?></strong></p>
    <p><a href="tienda.php">Seguir comprando</a></p>
  </div>
</div>
</body>
</html>
