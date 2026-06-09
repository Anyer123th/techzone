<?php
session_start();
include "conexion.php";

$mensaje = "";
if ($_POST) {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuario === "" || $password === "") {
        $mensaje = "Completa todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["id"] = (int)$user["id"];
                $_SESSION["usuario"] = $user["usuario"];
                $_SESSION["rol"] = $user["rol"];
                header("Location: dashboard.php");
                exit;
            }
        }
        $mensaje = "Datos incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="login.php">Tech Zone</a>
</header>

<div class="container">
  <div class="card">
    <h2>Iniciar sesión</h2>
    <?php if($mensaje){ echo "<p><strong>$mensaje</strong></p>"; } ?>
    <form method="post" autocomplete="off">
      <div style="margin:8px 0">
        <input name="usuario" required placeholder="Usuario" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
      </div>
      <div style="margin:8px 0">
        <input type="password" name="password" required placeholder="Contraseña">
      </div>
      <button type="submit">Entrar</button>
    </form>
    <p style="margin-top:10px">
      <a href="registro.php">Crear cuenta</a> ·
      <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
    </p>
    <small class="muted">Nota: en este proyecto escolar no se envían correos de recuperación.</small>
  </div>
</div>
</body>
</html>
