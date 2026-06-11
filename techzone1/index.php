<?php
session_start();
include "conexion.php";

function imagenProducto($nombre, $imagen){
    $mapa = [
        'laptop' => 'laptop.jpg', 'mouse' => 'mouse.jpg', 'teclado' => 'teclado.png',
        'monitor' => 'monitor.png', 'cockpit' => 'cockpit.jpg', 'volante' => 'volante.png'
    ];
    $imagen = trim((string)$imagen);
    if($imagen !== '' && file_exists(__DIR__ . '/img/' . $imagen)){
        return $imagen;
    }
    $clave = strtolower(trim((string)$nombre));
    foreach($mapa as $k => $v){
        if(strpos($clave, $k) !== false) return $v;
    }
    return 'monitor.png';
}

$res = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechZone - E-Commerce Corporativo</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Principal">
    <a href="index.php" aria-current="page">Inicio</a>
    <a href="contacto.php">Contacto</a>
    <a href="tienda.php">Tienda/Catalogo</a>
    <?php if(isset($_SESSION["usuario"])): ?>
        <a href="dashboard.php">Mi Panel</a>
        <?php if(($_SESSION["rol"] ?? "") === "admin"): ?>
            <a href="admin.php" style="color:#3b82f6 !important;">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" style="color: var(--danger) !important;">Salir</a>
    <?php else: ?>
        <a href="login.php" class="btn-nav">Iniciar Sesión</a>
    <?php endif; ?>
  </nav>
</header>

<main class="container">
  <section class="card" style="background: linear-gradient(135deg, rgba(37,99,235,0.1) 0%, rgba(17,17,24,0.9) 100%); text-align:center; padding: 4rem 2rem;">
    <h1>TechZone Corporación</h1>
    <p class="muted" style="max-width:800px; margin: 1rem auto 2rem auto;">
        Líderes en integración de ecosistemas avanzados para SimRacing y hardware informático de alta fidelidad.
    </p>
    
    <div class="corp-grid">
        <div class="corp-item">
            <h3>Misión</h3>
            <p class="muted">Proveer periféricos de simulación que optimicen el rendimiento de los pilotos virtuales locales.</p>
        </div>
        <div class="corp-item">
            <h3>Visión</h3>
            <p class="muted">Ser la tienda e-commerce de referencia tecnológica en el país, reconocida por nuestra robustez.</p>
        </div>
        <div class="corp-item">
            <h3>Valores</h3>
            <p class="muted">Innovación continua, precisión técnica, responsabilidad transaccional y accesibilidad.</p>
        </div>
    </div>
  </section>

  <section class="card">
    <h2>🎯 Recursos Multimedia de la Empresa</h2>
    <div class="media-grid">
        <div class="media-box">
            <h3>Video Institucional</h3>
            <video controls aria-label="Video promocional">
                <source src="video/promocion.mp4" type="video/mp4">
            </video>
        </div>
        <div class="media-box">
            <h3>Audio Guía</h3>
            <audio controls aria-label="Audio guía corporativo">
                <source src="audio/guia_corporativa.mp3" type="audio/mpeg">
            </audio>
        </div>
    </div>
  </section>

  <section>
    <h2 style="margin-bottom:1.5rem;">🛒 Catálogo de Hardware Premium</h2>
    <div class="grid">
      <?php while($p = $res->fetch_assoc()){ ?>
        <a href="producto.php?id=<?php echo (int)$p["id"]; ?>" class="product-card" aria-label="Ver <?php echo htmlspecialchars($p["nombre"]); ?>">
          <div>
            <img class="producto-img" src="img/<?php echo rawurlencode(imagenProducto($p["nombre"], $p["imagen"])); ?>" alt="Fotografía de <?php echo htmlspecialchars($p["nombre"]); ?>">
            <h3><?php echo htmlspecialchars($p["nombre"]); ?></h3>
          </div>
          <div style="margin-top: 1rem;">
            <p style="margin-bottom:1rem;"><strong style="color: var(--success); font-size:1.3rem;">$<?php echo number_format((float)$p["precio"], 2); ?></strong></p>
            <span class="btn-action" style="display:block;">Ver Ficha Técnica</span>
          </div>
        </a>
      <?php } ?>
    </div>
  </section>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Tel: (809) 555-0199</p>
    <div class="footer-links">
        <a href="index.php">Inicio</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="registro.php">Registro</a>
    </div>
</footer>
</body>
</html>