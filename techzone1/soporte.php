<?php
session_start();
include "conexion.php";

if(!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

$msg = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cliente = trim($_POST["cliente"] ?? "");
    $asunto = trim($_POST["asunto"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if($cliente != "" && $asunto != "" && $mensaje != ""){
        $stmt = $conn->prepare("INSERT INTO tickets_soporte(cliente, asunto, mensaje) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $cliente, $asunto, $mensaje);
        if($stmt->execute()){
            $msg = "Ticket de soporte técnico enviado correctamente.";
        } else {
            $msg = "Error al procesar el caso.";
        }
    } else {
        $msg = "Por favor llena todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte Técnico - Tech Zone</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="dashboard.php">Tech Zone</a>
  <nav aria-label="Menú principal">
    <a href="tienda.php">Tienda</a>
    <a href="dashboard.php">Panel</a>
    <a href="logout.php">Salir</a>
  </nav>
</header>

<main class="container">
    <div class="card">
        <h1>Formulario 3: Reporte de Incidencias y Hardware</h1>
        <p class="muted">Si tu hardware o equipo de simulación presenta fallas, genera un ticket inmediato.</p>
        
        <?php if($msg){ echo "<p style='margin-top:1rem; color:var(--accent);'><strong>$msg</strong></p>"; } ?>

        <form method="POST">
            <div class="form-group">
                <label for="cliente">Nombre del Solicitante:</label>
                <input type="text" id="cliente" name="cliente" value="<?php echo htmlspecialchars($_SESSION["usuario"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="asunto">Asunto del Ticket:</label>
                <input type="text" id="asunto" name="asunto" placeholder="Ej: Calibración FFB Volante" required>
            </div>
            <div class="form-group">
                <label for="mensaje">Descripción detallada del problema:</label>
                <textarea id="mensaje" name="mensaje" rows="5" placeholder="Escribe aquí los síntomas o errores observados..." required></textarea>
            </div>
            <button type="submit">Enviar Ticket a Revisión</button>
        </form>
    </div>
</main>
</body>
</html>