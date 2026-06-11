<?php
include "conexion.php";
$msg = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario = trim($_POST["usuario"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if($usuario === "" || $password === ""){
        $msg = "Error: Complete todos los apartados obligatorios.";
    } elseif(strlen($password) < 6) {
        $msg = "Error: Por seguridad, la contraseña debe poseer al menos 6 caracteres.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios(usuario, password, rol) VALUES(?, ?, 'user')");
        $stmt->bind_param("ss", $usuario, $hash);
        if($stmt->execute()){
            header("Location: login.php?registered=1");
            exit;
        } else {
            $msg = "El nombre de usuario ya está tomado en el servidor.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Cuentas - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav><a href="login.php">Iniciar Sesión</a></nav>
</header>
<main class="container">
  <div class="card" style="max-width:450px; margin:2rem auto;">
    <h1>Formulario de Registro 1: Alta de Cuenta</h1>
    <?php if($msg){ echo "<p style='color:var(--danger); margin-bottom:1rem;'><strong>$msg</strong></p>"; } ?>
    
    <form method="POST">
      <div class="form-group">
        <label for="usuario">Nombre de Usuario:</label>
        <input type="text" id="usuario" name="usuario" required minlength="4" placeholder="Ej: anyer_peralta">
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
      </div>
      <button type="submit">Registrar Mi Cuenta en DB</button>
    </form>
  </div>
</main>
</body>
</html>