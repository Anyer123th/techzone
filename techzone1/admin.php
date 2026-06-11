<?php
session_start();
include "conexion.php";

if(!isset($_SESSION["usuario"]) || ($_SESSION["rol"] ?? "") !== "admin") {
    header("Location: 403.html");
    exit;
}

$mensaje = "";

// LÓGICA NUEVA: ELIMINAR PRODUCTO
if(isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $stmt_del = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt_del->bind_param("i", $id_eliminar);
    if($stmt_del->execute()){
        $mensaje = "Éxito: Producto eliminado correctamente de la Base de Datos.";
    } else {
        $mensaje = "Error al eliminar el registro: " . $conn->error;
    }
}

// VALIDACIÓN DEL FORMULARIO 2 (Alta de Inventario)
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST["nombre"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");

    if($nombre === "" || $precio === "" || $descripcion === ""){
        $mensaje = "Error: Rellene todos los apartados obligatorios.";
    } elseif(!is_numeric($precio) || (float)$precio <= 0) {
        $mensaje = "Error: El precio debe ser un número positivo válido.";
    } else {
        $precio_num = (float)$precio;
        $stmt = $conn->prepare("INSERT INTO productos(nombre, precio, descripcion, imagen) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("sdss", $nombre, $precio_num, $descripcion, $imagen);
        if($stmt->execute()){
            $mensaje = "Éxito: Producto guardado e indexado correctamente.";
        } else {
            $mensaje = "Error en base de datos: " . $conn->error;
        }
    }
}

// Consultas dinámicas para recuperar los datos
$productos = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id DESC");
$usuarios = $conn->query("SELECT id, usuario, rol, fecha_registro FROM usuarios ORDER BY id DESC");
$tickets = $conn->query("SELECT id, cliente, asunto, fecha_creacion FROM tickets_soporte ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Base de Datos - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span> Admin</a>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="index.php">Ver Tienda</a>
    <a href="contacto.php">Contacto</a>
    <a href="logout.php" style="color:var(--danger) !important;">Salir</a>
  </nav>
</header>

<main class="container">
  <div class="card">
    <h2>Formulario de Registro 2: Inventario de Productos</h2>
    <p class="muted">Añade componentes y hardware tecnológico directamente en las tablas del servidor local.</p>
    <?php if($mensaje){ echo "<p style='color:var(--success); font-weight:bold; margin:1rem 0;'>$mensaje</p>"; } ?>

    <form method="POST">
      <div class="form-group">
        <label for="nombre">Nombre Comercial del Producto:</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Volante Direct Drive 12Nm">
      </div>
      <div class="form-group">
        <label for="precio">Precio Unitario (USD):</label>
        <input type="text" id="precio" name="precio" required placeholder="Ej: 549.99">
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción y Atributos Multimedia:</label>
        <textarea id="descripcion" name="descripcion" rows="3" required placeholder="Detalles técnicos y compatibilidad..."></textarea>
      </div>
      <div class="form-group">
        <label for="imagen">Nombre del archivo de imagen (Opcional):</label>
        <input type="text" id="imagen" name="imagen" placeholder="Ej: volante.png">
      </div>
      <button type="submit">Guardar Registro en Sistema</button>
    </form>
  </div>

  <div class="card">
    <h2>Información Almacenada 1: Inventario (`productos`)</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nombre Comercial</th>
            <th scope="col">Precio</th>
            <th scope="col">Archivo Imagen</th>
            <th scope="col">Acciones</th> </tr>
        </thead>
        <tbody>
          <?php while($p = $productos->fetch_assoc()){ ?>
            <tr>
              <td><?php echo $p["id"]; ?></td>
              <td><strong><?php echo htmlspecialchars($p["nombre"]); ?></strong></td>
              <td>$<?php echo number_format((float)$p["precio"], 2); ?></td>
              <td><code><?php echo htmlspecialchars($p["imagen"] ? $p["imagen"] : "default.png"); ?></code></td>
              <td>
                <a href="admin.php?eliminar=<?php echo $p['id']; ?>" 
                   style="color: var(--danger); font-weight: bold; text-decoration: none;" 
                   onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                   ❌ Eliminar
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h2>Información Almacenada 2: Cuentas (`usuarios`)</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nombre de Usuario</th>
            <th scope="col">Rol de Acceso</th>
            <th scope="col">Fecha Registro</th>
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
    <h2>Información Almacenada 3: Tickets de Soporte (`tickets_soporte`)</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th scope="col">ID Ticket</th>
            <th scope="col">Cliente Remitente</th>
            <th scope="col">Asunto del Reporte</th>
            <th scope="col">Estampa de Tiempo</th>
          </tr>
        </thead>
        <tbody>
          <?php while($t = $tickets->fetch_assoc()){ ?>
            <tr>
              <td><?php echo $t["id"]; ?></td>
              <td><strong><?php echo htmlspecialchars($t["cliente"]); ?></strong></td>
              <td><?php echo htmlspecialchars($t["asunto"]); ?></td>
              <td><?php echo $t["fecha_creacion"]; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body>
</html>