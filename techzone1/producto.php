<?php
session_start();
include "conexion.php";

// ELIMINADO: Eliminamos restricción estricta de inicio de sesión para visualización de fichas técnicas

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$stmt = $conn->prepare("SELECT id, nombre, precio, imagen, descripcion FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    header("Location: tienda.php");
    exit;
}

function imagenProducto($nombre, $imagen){
    $mapa = [
        'laptop' => 'laptop.jpg', 'mouse' => 'mouse.jpg', 'teclado' => 'teclado.png',
        'monitor' => 'monitor.jpg', 'audifonos' => 'Aud#U00edfonos.png', 'bocina' => 'Bocina.png',
        'camara' => 'camara.png', 'router' => 'router.jpg'
    ];
    $imagen = trim((string)$imagen);
    if($imagen !== '' && file_exists(__DIR__ . '/img/' . $imagen)){
        return $imagen;
    }
    $clave = strtolower(trim((string)$nombre));
    return $mapa[$clave] ?? '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto["nombre"]); ?> - Tech Zone</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .layout-detalle { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 2.5rem; margin-top: 1.5rem; }
        .visual-box { text-align: center; background-color: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; }
        .main-img { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 6px; transition: transform 0.3s ease; }
        .main-img:hover { transform: scale(1.03); }
        .info-box { display: flex; flex-direction: column; justify-content: space-between; }
        .precio-tag { font-size: 2.2rem; color: var(--success); font-weight: 700; margin: 1rem 0; }
        .tab-container { margin-top: 2rem; border-top: 1px solid var(--border); }
        .tab-menu { display: flex; gap: 0.5rem; margin-bottom: 1rem; margin-top: 1rem; }
        .tab-btn { background: var(--bg-input); color: var(--text-secondary); border: 1px solid var(--border); padding: 0.6rem 1.2rem; border-radius: 4px; font-size: 0.9rem; cursor: pointer; width: auto; }
        .tab-btn.active { background: var(--accent); color: white; border-color: var(--accent); }
        .tab-content { display: none; background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: 6px; }
        .tab-content.active { display: block; }
        .user-nav-status { display: flex; align-items: center; gap: 1rem; }
        .btn-login-header { background-color: var(--accent); color: white !important; padding: 0.4rem 1rem !important; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
<header>
  <a href="tienda.php">Tech Zone</a>
  <nav aria-label="Navegación" class="user-nav-status">
    <a href="tienda.php">← Volver</a>
    <?php if(isset($_SESSION["usuario"])): ?>
        <a href="dashboard.php">Mi Panel</a>
    <?php else: ?>
        <a href="login.php" class="btn-login-header">Iniciar Sesión</a>
    <?php endif; ?>
  </nav>
</header>

<main class="container">
    <div class="layout-detalle">
        <div class="visual-box">
            <?php $img = imagenProducto($producto["nombre"], $producto["imagen"]); ?>
            <?php if($img !== "") { ?>
                <img class="main-img" src="img/<?php echo rawurlencode($img); ?>" alt="Vista de <?php echo htmlspecialchars($producto["nombre"]); ?>">
            <?php } else { ?>
                <div class="producto-sin-img" style="height: 350px;">Galería multimedia no disponible</div>
            <?php } ?>
        </div>

        <div class="info-box card">
            <div>
                <span class="badge">Hardware Destacado</span>
                <h1 style="margin-top: 0.5rem; font-size: 2rem;"><?php echo htmlspecialchars($producto["nombre"]); ?></h1>
                <p class="precio-tag">$<?php echo number_format((float)$producto["precio"], 2); ?></p>
            </div>

            <div>
                <a href="comprar.php?id=<?php echo (int)$producto["id"]; ?>" class="btn-action" style="display: block; font-size: 1.1rem; padding: 1rem;">
                    🛒 Comprar Ahora
                </a>
            </div>
        </div>
    </div>

    <div class="tab-container">
        <div class="tab-menu" role="tablist">
            <button class="tab-btn active" onclick="cambiarTab(event, 'tab-desc')" role="tab" aria-selected="true">Descripción</button>
            <button class="tab-btn" onclick="cambiarTab(event, 'tab-specs')" role="tab" aria-selected="false">Ficha Técnica</button>
        </div>

        <div id="tab-desc" class="tab-content active" role="tabpanel">
            <p style="color: var(--text-secondary);"><?php echo nl2br(htmlspecialchars($producto["descripcion"] ?? "Sin descripción.")); ?></p>
        </div>

        <div id="tab-specs" class="tab-content" role="tabpanel">
            <div class="table-container">
                <table>
                    <tr><th scope="row" style="width: 30%;">Garantía</th><td>12 Meses</td></tr>
                    <tr><th scope="row">Distribución</th><td>TechZone Store</td></tr>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function cambiarTab(evt, tabId) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
        evt.currentTarget.setAttribute('aria-selected', 'true');
    }
</script>
</body>
</html>