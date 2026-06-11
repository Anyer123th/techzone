<?php
session_start();
include "conexion.php";

if(!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

$msg = "";
// VALIDACIÓN DEL FORMULARIO 3 (Back-end)
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cliente = $_SESSION["usuario"];
    $asunto = trim($_POST["asunto"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if($asunto === "" || $mensaje === ""){
        $msg = "Error: Todos los campos del formulario son requeridos.";
    } elseif(strlen($asunto) < 5) {
        $msg = "Error: El asunto debe ser más descriptivo (mínimo 5 caracteres).";
    } else {
        $stmt = $conn->prepare("INSERT INTO tickets_soporte(cliente, asunto, mensaje) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $cliente, $asunto, $mensaje);
        if($stmt->execute()){
            $msg = "Éxito: Su reporte de incidencia ha sido inyectado en la base de datos.";
        } else {
            $msg = "Error operacional en el servidor.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soporte Técnico - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav>
    <a href="index.php">Inicio</a>
    <a href="dashboard.php">Mi Panel</a>
  </nav>
</header>

<main class="container">
  <div class="card" style="max-width: 650px; margin: 0 auto;">
    <h1>Formulario de Registro 3: Reporte de Incidencias</h1>
    <p class="muted">Si tu hardware presenta anomalías mecánicas o de calibración, genera un ticket inmediato.</p>
    
    <?php if($msg){ echo "<p style='color: var(--accent); font-weight:bold; margin: 1rem 0;'>$msg</p>"; } ?>

    <form method="POST">
      <div class="form-group">
        <label for="asunto">Asunto Breve del Fallo:</label>
        <input type="text" id="asunto" name="asunto" required minlength="5" placeholder="Ej: Holgura en eje central">
      </div>
      <div class="form-group">
        <label for="mensaje">Explicación Pormenorizada de los Síntomas:</label>
        <textarea id="mensaje" name="mensaje" rows="5" required minlength="15" placeholder="Indica detalladamente los síntomas observados..."></textarea>
      </div>
      <button type="submit">Enviar Ticket a Revisión Técnica</button>
    </form>
  </div>
</main>
</body>
</html>