<?php
session_start();
include "conexion.php";

// ELIMINADO: Ya no redirigimos a la fuerza al login.php. Permitimos ver la tienda.

function imagenProducto($nombre, $imagen){
    $mapa = [
        'laptop' => 'laptop.jpg',
        'mouse' => 'mouse.jpg',
        'teclado' => 'teclado.png',
        'monitor' => 'monitor.jpg',
        'audifonos' => 'Aud#U00edfonos.png',
        'bocina' => 'Bocina.png',
        'camara' => 'camara.png',
        'router' => 'router.jpg'
    ];
    $imagen = trim((string)$imagen);
    if($imagen !== '' && file_exists(__DIR__ . '/img/' . $imagen)){
        return $imagen;
    }
    $clave = strtolower(trim((string)$nombre));
    return $mapa[$clave] ?? '';
}

$res = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tienda - Tech Zone</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    .product-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s, border-color 0.2s;
    }
    .product-link:hover .card {
        border-color: var(--accent);
        transform: translateY(-4px);
    }
    /* Alineación estilo Amazon para la derecha */
    .user-nav-status {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .btn-login-header {
        background-color: var(--accent);
        color: white !important;
        padding: 0.4rem 1rem !important;
        border-radius: 4px;
        font-weight: bold;
    }
  </style>
</head>
<body>
<header>
  <a href="tienda.php">Tech Zone</a>
  <nav aria-label="Navegación principal" class="user-nav-status">
    <?php if(isset($_SESSION["usuario"])): ?>
        <span style="color: var(--success); font-size: 0.9rem;">Hola, <strong><?php echo htmlspecialchars($_SESSION["usuario"]); ?></strong></span>
        <a href="dashboard.php">Mi Panel</a>
        <a href="soporte.php">Soporte</a>
        <a href="logout.php" style="color: var(--danger);">Salir</a>
    <?php else: ?>
        <span class="muted" style="font-size: 0.9rem;">Invitado</span>
        <a href="login.php" class="btn-login-header">Identifícate / Iniciar Sesión</a>
    <?php endif; ?>
  </nav>
</header>

<main class="container">
  <section class="card" aria-labelledby="section-title">
    <h2 id="section-title">🛍️ Catálogo de Hardware de Alto Rendimiento</h2>
    <p class="muted">Explora libremente nuestro inventario. Identifícate desde el menú superior para procesar órdenes.</p>
  </section>

  <?php if(!$res || $res->num_rows === 0){ ?>
    <div class="card">
      <p>No hay artículos registrados en este momento.</p>
    </div>
  <?php } else { ?>
    <div class="grid">
      <?php while($p = $res->fetch_assoc()){ ?>
        <a href="producto.php?id=<?php echo (int)$p["id"]; ?>" class="product-link" aria-label="Ver detalles de <?php echo htmlspecialchars($p["nombre"]); ?>">
          <article class="card" style="height: 100%; margin-bottom:0;">
            <h3><?php echo htmlspecialchars($p["nombre"]); ?></h3>
            <p>Precio: <strong style="color: var(--success); font-size: 1.2rem;">$<?php echo number_format((float)$p["precio"], 2); ?></strong></p>
            
            <?php $img = imagenProducto($p["nombre"], $p["imagen"]); ?>
            <?php if($img !== "") { ?>
              <img class="producto-img" src="img/<?php echo rawurlencode($img); ?>" alt="Fotografía de <?php echo htmlspecialchars($p["nombre"]); ?>">
            <?php } else { ?>
              <div class="producto-sin-img" aria-hidden="true">Sin imagen de referencia</div>
            <?php } ?>
            
            <p style="margin-top: 15px; color: var(--accent); font-weight: bold; text-align: right; font-size: 0.9rem;">
               Ver más detalles →
            </p>
          </article>
        </a>
      <?php } ?>
    </div>
  <?php } ?>
</main>
</body>
</html>