<?php
session_start();
include "conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Consulta preparada para evitar inyecciones SQL
$stmt = $conn->prepare("SELECT id, nombre, precio, descripcion, imagen FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();

// Si el producto no existe en la base de datos, redirige a la tienda
if (!$producto) {
    header("Location: tienda.php");
    exit;
}

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($producto["nombre"]); ?> - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    /* Estilos UX exclusivos de la vitrina multimedia interactiva */
    .detail-layout { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
        gap: 3rem; 
        margin-top: 2rem; 
    }
    .img-box { 
        background-color: var(--bg-card); 
        border: 1px solid var(--border); 
        border-radius: 12px; 
        padding: 2rem; 
        text-align: center; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .img-box img { 
        max-width: 100%; 
        max-height: 350px; 
        object-fit: contain; 
        border-radius: 8px; 
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    }
    /* Efecto zoom interactivo en la imagen multimedia */
    .img-box img:hover { 
        transform: scale(1.05); 
    }
    .tab-menu { 
        display: flex; 
        gap: 0.5rem; 
        margin: 3rem 0 1rem 0; 
        border-bottom: 1px solid var(--border); 
        padding-bottom: 0.5rem; 
    }
    .tab-btn { 
        background: none; 
        color: var(--text-muted); 
        border: none; 
        padding: 0.5rem 1rem; 
        cursor: pointer; 
        width: auto; 
        font-weight: 700; 
        font-size: 1rem;
    }
    .tab-btn.active { 
        color: #3b82f6; 
        border-bottom: 3px solid #3b82f6; 
    }
    .tab-content { 
        display: none; 
        background-color: var(--bg-card); 
        border: 1px solid var(--border); 
        padding: 2rem; 
        border-radius: 12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .tab-content.active { 
        display: block; 
    }
  </style>
</head>
<body>
<header>
  <a href="tienda.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Principal">
    <a href="tienda.php">Inicio / Tienda</a>
    <a href="contacto.php">Contacto</a>
    <?php if(isset($_SESSION["usuario"])): ?>
        <a href="dashboard.php">Mi Panel</a>
        <?php if(($_SESSION["rol"] ?? "") === "admin"): ?>
            <a href="admin.php" style="color: #3b82f6 !important;">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" style="color: var(--danger) !important;">Salir</a>
    <?php else: ?>
        <a href="login.php" class="btn-nav">Iniciar Sesión</a>
    <?php endif; ?>
  </nav>
</header>

<main class="container">
  <div class="detail-layout">
    <div class="img-box">
      <img src="img/<?php echo htmlspecialchars(imagenProducto($producto["nombre"], $producto["imagen"])); ?>" alt="Hardware <?php echo htmlspecialchars($producto["nombre"]); ?>">
      <p class="muted" style="margin-top: 1rem; font-size: 0.85rem;">💡 Pasa el cursor sobre el componente para ampliar la imagen.</p>
    </div>

    <div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">
      <div>
        <span class="badge" style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 0.3rem 0.8rem; border-radius: 20px;">Componente Verificado</span>
        <h1 style="margin-top:1.5rem; font-size:2.2rem;"><?php echo htmlspecialchars($producto["nombre"]); ?></h1>
        <p style="font-size: 2.4rem; color: var(--success); font-weight: 800; margin: 1.5rem 0;">$<?php echo number_format((float)$producto["precio"], 2); ?></p>
        <p class="muted">Garantía Corporativa: <span style="color: var(--success); font-weight: bold;">12 Meses de Fábrica</span></p>
        <p class="muted" style="margin-top: 0.5rem;">Disponibilidad: <span style="color: var(--success); font-weight: bold;">En Stock Local</span></p>
      </div>
      <a href="comprar.php?id=<?php echo (int)$producto["id"]; ?>" class="btn-action" style="padding:1.2rem; font-size:1.1rem; font-weight: 700; margin-top: 2rem;">🛒 Proceder a la Compra</a>
    </div>
  </div>

  <div class="tab-menu" role="tablist">
    <button class="tab-btn active" onclick="switchTab(event, 'desc')" role="tab" aria-selected="true">Descripción del Hardware</button>
    <button class="tab-btn" onclick="switchTab(event, 'specs')" role="tab" aria-selected="false">Ficha Técnica de Ingeniería</button>
  </div>

  <div id="desc" class="tab-content active" role="tabpanel">
    <h3 style="margin-bottom: 1rem; color: #3b82f6;">Información Comercial</h3>
    <p style="color: var(--text-muted); font-size: 1.05rem; text-align: justify;"><?php echo nl2br(htmlspecialchars($producto["descripcion"])); ?></p>
  </div>
  
  <div id="specs" class="tab-content" role="tabpanel">
    <h3 style="margin-bottom: 1rem; color: #3b82f6;">Especificaciones Técnicas</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th scope="col" style="width: 40%;">Parámetro Técnico</th>
                    <th scope="col">Detalle Oficial Corporativo</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>Origen del Hardware</strong></td><td>Importación Certificada Premium</td></tr>
                <tr><td><strong>Compatibilidad del Ecosistema</strong></td><td>Plug & Play universal en PC, Windows 10/11 y Consolas</td></tr>
                <tr><td><strong>Soporte Técnico Especializado</strong></td><td>Asistencia directa mediante nuestro apartado de Contacto</td></tr>
            </tbody>
        </table>
    </div>
  </div>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Escuela Técnica de Informática</p>
    <div class="footer-links">
        <a href="tienda.php">Inicio / Tienda</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="dashboard.php">Mi Panel Privado</a>
    </div>
</footer>

<script>
function switchTab(e, id) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
    document.getElementById(id).classList.add('active');
    e.currentTarget.classList.add('active');
    e.currentTarget.setAttribute('aria-selected', 'true');
}
</script>
</body>
</html>