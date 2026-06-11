<?php
session_start();
include "conexion.php";

$msg = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cliente = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : trim($_POST["nombre_invitado"] ?? "Invitado");
    $asunto = trim($_POST["asunto"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if($asunto !== "" && $mensaje !== ""){
        $stmt = $conn->prepare("INSERT INTO tickets_soporte(cliente, asunto, mensaje) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $cliente, $asunto, $mensaje);
        if($stmt->execute()){
            $msg = "Éxito: Su mensaje de contacto ha sido almacenado en la Base de Datos.";
        } else {
            $msg = "Error operacional al guardar.";
        }
    } else {
        $msg = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav>
    <a href="index.php">Inicio</a>
    <a href="contacto.php" aria-current="page">Contacto</a>
  </nav>
</header>

<main class="container">
  <div class="card" style="max-width: 650px; margin: 0 auto;">
    <h1>Área de Contacto Corporativo</h1>
    <p class="muted">Envíanos tus dudas comerciales o solicitudes de soporte directamente al servidor.(849)-340-1009</p>
    
    <?php if($msg){ echo "<p class='status-msg' style='color: var(--accent);'>$msg</p>"; } ?>

    <form method="POST">
      <?php if(!isset($_SESSION["usuario"])): ?>
        <div class="form-group">
            <label for="nombre_invitado">Tu Nombre Completo:</label>
            <input type="text" id="nombre_invitado" name="nombre_invitado" required placeholder="Ej: Anyer Peralta">
        </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="asunto">Asunto del Mensaje:</label>
        <input type="text" id="asunto" name="asunto" required placeholder="Ej: Consulta sobre disponibilidad de Volantes">
      </div>
      <div class="form-group">
        <label for="mensaje">Cuerpo del Mensaje / Comentario:</label>
        <textarea id="mensaje" name="mensaje" rows="5" required placeholder="Escribe aquí tu duda..."></textarea>
      </div>
      <button id=boton type="submit">Enviar Mensaje de Contacto</button>
    </form>
  </div>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <div class="footer-links">
        <a href="index.php">Inicio</a>
        <a href="contacto.php">Contacto</a>
    </div>
</footer>
</body>
</html>