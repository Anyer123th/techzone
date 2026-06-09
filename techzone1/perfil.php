<?php
session_start();
include "conexion.php";
if(!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$id = (int)$_SESSION["id"];

if($_POST){
    $pass1 = $_POST["password1"] ?? "";
    $pass2 = $_POST["password2"] ?? "";

    if(strlen($pass1) < 4){
        $mensaje = "La nueva contraseña debe tener al menos 4 caracteres.";
    } elseif($pass1 !== $pass2){
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $id);
        if($stmt->execute()){
            $mensaje = "Contraseña actualizada.";
        } else {
            $mensaje = "Error: " . $conn->error;
        }
    }
}

$stmt = $conn->prepare("SELECT usuario, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="dashboard.php">Tech Zone</a>
  <a href="tienda.php">Tienda</a>
  <a href="logout.php">Salir</a>
</header>

<div class="container">
  <div class="card">
    <h2>Perfil</h2>
    <p>Usuario: <strong><?php echo htmlspecialchars($user["usuario"] ?? ""); ?></strong></p>
    <p>Rol: <span class="badge"><?php echo htmlspecialchars($user["rol"] ?? "user"); ?></span></p>
  </div>

  <div class="card">
    <h3>Cambiar contraseña</h3>
    <?php if($mensaje){ echo "<p><strong>$mensaje</strong></p>"; } ?>
    <form method="post">
      <div style="margin:8px 0">
        <input type="password" name="password1" placeholder="Nueva contraseña" required>
      </div>
      <div style="margin:8px 0">
        <input type="password" name="password2" placeholder="Repetir contraseña" required>
      </div>
      <button>Guardar</button>
    </form>
  </div>
</div>
</body>
</html>
