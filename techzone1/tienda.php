<?php
session_start();
include "conexion.php";

// 1. CAPTURA Y VALIDACIÓN DE PARÁMETROS (Búsqueda y Ordenamiento)
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$orden = isset($_GET['orden']) ? trim($_GET['orden']) : 'recientes';

// Mapeo seguro de opciones de ordenamiento para evitar inyecciones SQL
$ordenes_permitidos = [
    'precio_asc'  => 'precio ASC',    // Precio: Bajo a Alto
    'precio_desc' => 'precio DESC',   // Precio: Alto a Bajo
    'nombre_asc'  => 'nombre ASC',    // Alfabético: A-Z
    'recientes'   => 'id DESC'        // Más recientes primero
];

// Si la opción no existe en el mapa, usamos 'recientes' por defecto
$sql_order = isset($ordenes_permitidos[$orden]) ? $ordenes_permitidos[$orden] : 'id DESC';

// 2. LÓGICA DEL BACKEND CON CONSULTAS PREPARADAS (Filtrado Dinámico)
if ($buscar !== '') {
    // Si hay un término de búsqueda, filtramos usando LIKE junto al orden seleccionado
    $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE nombre LIKE ? ORDER BY $sql_order");
    $termino = "%" . $buscar . "%";
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    // Si no hay búsqueda, traemos todo el stock ordenado dinámicamente
    $res = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY $sql_order");
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
  <title>Tienda Premium - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    /* ESTILOS EXCLUSIVOS PARA LA BARRA DE FILTROS AVANZADA */
    .filter-zone {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
    }

    .search-wrapper {
        flex: 1;
        min-width: 280px;
        position: relative;
    }

    .search-input-premium {
        width: 100%;
        padding: 0.85rem 1rem 0.85rem 2.8rem;
        background-color: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: #ffffff;
        font-size: 1rem;
    }

    .search-icon-label {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    .sort-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 240px;
    }

    .sort-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .select-premium {
        background-color: var(--bg-input);
        color: #ffffff;
        border: 1px solid var(--border);
        padding: 0.85rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .btn-submit-filters {
        width: auto;
        padding: 0.85rem 1.8rem;
        font-weight: 700;
    }

    .clear-link {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-block;
        margin-top: 1rem;
        transition: opacity 0.2s;
    }
    .clear-link:hover {
        opacity: 0.8;
    }
  </style>
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Principal">
    <a href="tienda.php" aria-current="page">Inicio / Tienda</a>
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
  <section class="card" style="background: linear-gradient(135deg, rgba(37,99,235,0.1) 0%, rgba(17,17,24,0.9) 100%); text-align:center; padding: 3rem 2rem; margin-bottom: 2rem;">
    <h1>E-Commerce Hardware de Competición</h1>
    <p class="muted">Usa los controles inteligentes inferiores para filtrar e indexar el catálogo en tiempo real.</p>
  </section>

  <section class="filter-zone" aria-label="Filtros del Catálogo">
    <form action="tienda.php" method="GET" class="filter-form">
        
        <div class="search-wrapper">
            <span class="search-icon-label" aria-hidden="true">🔍</span>
            <input 
                type="text" 
                name="buscar" 
                class="search-input-premium" 
                placeholder="Buscar por nombre del hardware..." 
                value="<?php echo htmlspecialchars($buscar); ?>"
                aria-label="Escribe el nombre del dispositivo a buscar"
            >
        </div>

        <div class="sort-wrapper">
            <label for="orden" class="sort-label">Ordenar por:</label>
            <select name="orden" id="orden" class="select-premium" aria-label="Criterio de ordenamiento de los productos">
                <option value="recientes" <?php echo $orden === 'recientes' ? 'selected' : ''; ?>>Últimos agregados</option>
                <option value="precio_asc" <?php echo $orden === 'precio_asc' ? 'selected' : ''; ?>>Precio: Bajo a Alto</option>
                <option value="precio_desc" <?php echo $orden === 'precio_desc' ? 'selected' : ''; ?>>Precio: Alto a Bajo</option>
                <option value="nombre_asc" <?php echo $orden === 'nombre_asc' ? 'selected' : ''; ?>>Nombre: A - Z</option>
            </select>
        </div>

        <button type="submit" class="btn-submit-filters">Aplicar Filtros</button>
    </form>
  </section>

  <section aria-labelledby="catalog-title">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <?php if($buscar !== ''): ?>
            <h2 id="catalog-title">Resultados de búsqueda para: "<?php echo htmlspecialchars($buscar); ?>"</h2>
            <a href="tienda.php" class="clear-link">← Limpiar filtros y ver todo</a>
        <?php else: ?>
            <h2 id="catalog-title">📦 Catálogo Completo de Hardware</h2>
        <?php endif; ?>
        
        <p class="muted" style="font-weight: 600;">Registros encontrados: <span style="color: var(--success);"><?php echo $res->num_rows; ?></span></p>
    </div>

    <div class="grid">
      <?php if(!$res || $res->num_rows === 0){ ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
          <p class="muted" style="font-size: 1.25rem; margin-bottom: 1.5rem;">No tenemos registros que coincidan exactamente con tu criterio actual.</p>
          <a href="tienda.php" class="btn-action" style="max-width: 250px; display: inline-block;">Restablecer Filtros</a>
        </div>
      <?php } else { ?>
        <?php while($p = $res->fetch_assoc()){ ?>
          <a href="producto.php?id=<?php echo (int)$p["id"]; ?>" class="product-card" aria-label="Detalles completos de <?php echo htmlspecialchars($p["nombre"]); ?>">
            <div>
              <img class="producto-img" src="img/<?php echo rawurlencode(imagenProducto($p["nombre"], $p["imagen"])); ?>" alt="Fotografía del hardware <?php echo htmlspecialchars($p["nombre"]); ?>">
              <h3><?php echo htmlspecialchars($p["nombre"]); ?></h3>
            </div>
            <div style="margin-top: 1.5rem;">
              <p style="margin-bottom:1rem;"><strong style="color: var(--success); font-size:1.4rem;">$<?php echo number_format((float)$p["precio"], 2); ?></strong></p>
              <span class="btn-action" style="display:block; background-color: rgba(255,255,255,0.02); border:1px solid var(--border);">Ver Ficha e Ingeniería</span>
            </div>
          </a>
        <?php } ?>
      <?php } ?>
    </div>
  </section>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Módulo Técnico Escolar</p>
    <div class="footer-links">
        <a href="tienda.php">Inicio / Tienda</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="dashboard.php">Mi Panel Privado</a>
    </div>
</footer>
</body>
</html>