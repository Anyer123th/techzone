<?php
session_start();
include "conexion.php";

if(!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
if(($_SESSION["rol"] ?? "user") !== "admin") {
    die("No autorizado. <a href='dashboard.php'>Volver</a>");
}

$mensaje = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST["nombre"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");

    if($nombre === "" || $precio === ""){
        $mensaje = "Completa nombre y precio.";
    } elseif(!is_numeric($precio)) {
        $mensaje = "El precio debe ser numérico.";
    } else {
        $precio_num = (float)$precio;
        $stmt = $conn->prepare("INSERT INTO productos(nombre, precio, imagen) VALUES(?, ?, ?)");
        $stmt->bind_param("sds", $nombre, $precio_num, $imagen);
        if($stmt->execute()){
            $mensaje = "Producto agregado correctamente a la base de datos.";
        } else {
            $mensaje = "Error al insertar: " . $conn->error;
        }
    }
}

// Consultas para extraer los datos de las 3 tablas solicitadas
$productos = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id DESC");
$usuarios = $conn->query("SELECT id, usuario, rol, fecha_registro FROM usuarios ORDER BY id DESC");
$tickets = $conn->query("SELECT id, cliente, asunto, fecha_creacion FROM tickets_soporte ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="dashboard.php">Tech Zone</a>
  <nav aria-label="Navegación de administración">
    <a href="dashboard.php">Dashboard</a>
    <a href="tienda.php">Tienda</a>
    <a href="soporte.php">Crear Ticket</a>
    <a href="logout.php">Salir</a>
  </nav>
</header>

<main class="container">
  <div class="card">
    <h1>Panel de Administración del Servidor</h1>
    <p class="muted">Gestión local del sistema sin requerir acceso externo a herramientas del motor DB.</p>
    <?php if($mensaje){ echo "<p style='color: var(--success); margin-top: 1rem;'><strong>$mensaje</strong></p>"; } ?>

    <h2 style="margin-top:2rem;">Formulario: Agregar nuevo producto</h2>
    <form method="post">
      <div class="form-group">
        <label for="nombre">Nombre del Dispositivo:</label>
        <input id="nombre" name="nombre" placeholder="Ej: Teclado Mecánico RGB" required>
      </div>
      <div class="form-group">
        <label for="precio">Precio Comercial (USD):</label>
        <input id="precio" name="precio" placeholder="Ej: 1200" required>
      </div>
      <div class="form-group">
        <label for="imagen">Nombre del archivo de imagen (Opcional):</label>
        <input id="imagen" name="imagen" placeholder="Ej: teclado.png">
      </div>
      <button type="submit">Guardar en Inventario</button>
    </form>
  </div>

  <div class="card">
    <h2>Tabla de Datos 1: Inventario de Productos (`productos`)</h2>
    <?php if(!$productos || $productos->num_rows === 0){ ?>
      <p class="muted">No hay productos en stock.</p>
    <?php } else { ?>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Nombre del Producto</th>
              <th scope="col">Precio</th>
              <th scope="col">Identificador de Imagen</th>
            </tr>
          </thead>
          <tbody>
          <?php while($p = $productos->fetch_assoc()){ ?>
            <tr>
              <td><?php echo (int)$p["id"]; ?></td>
              <td><strong><?php echo htmlspecialchars($p["nombre"]); ?></strong></td>
              <td>$<?php echo number_format((float)$p["precio"], 2); ?></td>
              <td>`<?php echo htmlspecialchars($p["imagen"] ?? "sin-imagen"); ?>`</td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>

  <div class="card">
    <h2>Tabla de Datos 2: Control de Cuentas (`usuarios`)</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre de Usuario</th>
                    <th scope="col">Rol Asignado</th>
                    <th scope="col">Fecha de Alta</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $usuarios->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $u["id"]; ?></td>
                    <td><?php echo htmlspecialchars($u["usuario"]); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($u["rol"]); ?></span></td>
                    <td><?php echo $u["fecha_registro"]; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
  </div>

  <div class="card">
    <h2>Tabla de Datos 3: Historial de Casos (`tickets_soporte`)</h2>
    <?php if(!$tickets || $tickets->num_rows === 0){ ?>
      <p class="muted">No se han registrado reportes técnicos de clientes.</p>
    <?php } else { ?>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Remitente</th>
              <th scope="col">Asunto</th>
              <th scope="col">Fecha de Envío</th>
            </tr>
          </thead>
          <tbody>
          <?php while($t = $tickets->fetch_assoc()){ ?>
            <tr>
              <td><?php echo $t["id"]; ?></td>
              <td><?php echo htmlspecialchars($t["cliente"]); ?></td>
              <td><?php echo htmlspecialchars($t["asunto"]); ?></td>
              <td><?php echo $t["fecha_creacion"]; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>
</main>
</body>
</html>