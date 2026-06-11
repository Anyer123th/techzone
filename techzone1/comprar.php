<?php
session_start();
include "conexion.php";

// Si el usuario navega de forma anónima (estilo Amazon), lo redirige al login antes de comprar
if(!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$uid = (int)$_SESSION["id"];
$pid = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($pid <= 0) {
    die("Error: Parámetro de hardware no válido. <a href='tienda.php'>Volver a tienda</a>");
}

// 1. Verificación en la base de datos de que el producto realmente existe
$stmt = $conn->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();

if (!$prod) {
    die("Error: El componente solicitado no se encuentra indexado. <a href='tienda.php'>Volver</a>");
}

// 2. INSERCIÓN TRANSACCIONAL REAL (Registra la compra en la tabla relacional)
// Nota escolar: Asegúrate de tener la tabla `compras` creada en tu base de datos
$stmt2 = $conn->prepare("INSERT INTO compras(usuario_id, producto_id) VALUES(?, ?)");
$stmt2->bind_param("ii", $uid, $pid);
$stmt2->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compra Confirmada - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="tienda.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Principal">
    <a href="tienda.php">Inicio / Tienda</a>
    <a href="contacto.php">Contacto</a>
    <a href="dashboard.php">Mi Panel</a>
    <a href="logout.php" style="color: var(--danger) !important;">Cerrar Sesión</a>
  </nav>
</header>

<main class="container">
  <div class="card" style="max-width: 550px; margin: 4rem auto; text-align: center; border-color: var(--success); box-shadow: 0 0 25px rgba(16,185,129,0.15);">
    <div style="font-size: 3.5rem; margin-bottom: 1rem;">✅</div>
    <h1 style="color: var(--success); margin-bottom: 0.5rem;">Transacción Completada</h1>
    <p class="muted">Tu orden de hardware ha sido registrada e indexada correctamente en el servidor local.</p>
    
    <div style="background-color: var(--bg-input); border: 1px solid var(--border); padding: 1.5rem; border-radius: 8px; margin: 2rem 0; text-align: left;">
        <h3 style="border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1rem; color: #3b82f6;">🧾 Resumen de Factura</h3>
        <p style="margin-bottom: 0.5rem; color: var(--text-main);">Artículo: <strong><?php echo htmlspecialchars($prod["nombre"]); ?></strong></p>
        <p style="margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Cliente ID del Servidor: <code>#<?php echo $uid; ?></code></p>
        <p style="margin-top: 1rem; font-size: 1.25rem; color: var(--success); font-weight: bold;">Monto Debitado: $<?php echo number_format((float)$prod["precio"], 2); ?> USD</p>
    </div>

    <a href="tienda.php" class="btn-action" style="font-weight: 700; padding: 1rem;">Seguir Inspeccionando Catálogo</a>
  </div>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Técnico en Informática</p>
    <div class="footer-links">
        <a href="tienda.php">Inicio / Tienda</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="dashboard.php">Mi Panel Privado</a>
    </div>
</footer>
</body>
</html>