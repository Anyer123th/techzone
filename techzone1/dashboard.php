<?php
session_start();
if(!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="dashboard.php">Tech Zone</a>
  <a href="tienda.php">Tienda</a>
  <a href="perfil.php">Perfil</a>
  <a href="logout.php">Salir</a>
</header>

<div class="container">
  <div class="card">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?> 👋</h1>
    <p>Rol: <span class="badge"><?php echo htmlspecialchars($_SESSION["rol"] ?? "user"); ?></span></p>

    <p>
      <a href="tienda.php">Ir a la tienda</a>
    </p>

    <?php if(($_SESSION["rol"] ?? "user") === "admin"){ ?>
      <p><a href="admin.php">Panel Admin</a></p>
    <?php } ?>

    <small class="muted">Proyecto escolar: login + tienda + panel admin sencillo.</small>
  </div>
</div>
</body>
</html>
