<?php
session_start();
include "conexion.php";

$mensaje = "";
if ($_POST) {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if (strlen($usuario) < 3) {
        $mensaje = "El usuario debe tener al menos 3 caracteres.";
    } elseif (strlen($password) < 4) {
        $mensaje = "La contraseña debe tener al menos 4 caracteres.";
    } else {
        // verificar duplicado
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $mensaje = "Ese usuario ya existe. Elige otro.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO usuarios(usuario, password, rol) VALUES(?, ?, 'user')");
            $stmt2->bind_param("ss", $usuario, $hash);
            if ($stmt2->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $mensaje = "Error al registrar: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="login.php">Tech Zone</a>
</header>

<div class="container">
  <div class="card">
    <h2>Crear cuenta</h2>
    <?php if($mensaje){ echo "<p><strong>$mensaje</strong></p>"; } ?>
    <form method="post" autocomplete="off">
      <div style="margin:8px 0">
        <input name="usuario" required placeholder="Usuario" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
      </div>
      <div style="margin:8px 0">
        <input type="password" name="password" required placeholder="Contraseña">
      </div>
      <button type="submit">Registrar</button>
    </form>
    <p style="margin-top:10px"><a href="login.php">Volver al login</a></p>
  </div>
</div>
</body>
</html>
