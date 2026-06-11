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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Panel - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    /* Estilos UX exclusivos para el Dashboard interactivo */
    .welcome-banner {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(16, 16, 24, 0.9) 100%);
        border: 1px solid rgba(37, 99, 235, 0.3);
        box-shadow: 0 0 25px rgba(37, 99, 235, 0.1);
    }
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .action-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.2s, border-color 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .action-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent);
        background: rgba(37, 99, 235, 0.05);
    }
    .action-card h3 {
        color: #ffffff;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    .action-card p {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
  </style>
</head>
<body>
<header>
  <a href="index.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Privado">
    <a href="index.php">Inicio</a>
    <a href="contacto.php">Contacto</a>
    <a href="dashboard.php" aria-current="page">Mi Panel</a>
    <?php if(($_SESSION["rol"] ?? "") === "admin"): ?>
      <a href="admin.php" style="color: #3b82f6 !important;">Admin Panel</a>
    <?php endif; ?>
    <a href="logout.php" style="color: var(--danger) !important;">Salir</a>
  </nav>
</header>

<main class="container">
  <section class="card welcome-banner">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">Bienvenido de vuelta, <?php echo htmlspecialchars($_SESSION["usuario"]); ?> 👋</h1>
            <p class="muted">Has ingresado correctamente al entorno corporativo de simulación y hardware local.</p>
        </div>
        <div>
            <span class="badge" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                Rol: <?php echo htmlspecialchars($_SESSION["rol"] ?? "user"); ?>
            </span>
        </div>
    </div>
  </section>

  <section aria-labelledby="actions-heading">
    <h2 id="actions-heading" style="font-size: 1.4rem; margin-bottom: 1rem;">🎛️ Panel de Accesos Directos</h2>
    <div class="quick-actions">
        
        <a href="index.php" class="action-card" aria-label="Ir al catálogo de productos">
            <h3>🛒 Explorar Tienda</h3>
            <p>Inspecciona componentes multimedia y hardware en stock.</p>
        </a>

        <a href="contacto.php" class="action-card" aria-label="Ir al formulario de contacto y soporte">
            <h3>📩 Centro de Contacto</h3>
            <p>Envía dudas o reportes directamente al servidor local.</p>
        </a>

        <?php if(($_SESSION["rol"] ?? "") === "admin"): ?>
        <a href="admin.php" class="action-card" style="border-color: rgba(37, 99, 235, 0.4);" aria-label="Ir al panel de administración">
            <h3 style="color: #3b82f6;">⚙️ Base de Datos</h3>
            <p>Monitorea y lee las 3 tablas relacionales de MySQL sin phpMyAdmin.</p>
        </a>
        <?php endif; ?>

        <a href="logout.php" class="action-card" style="border-color: rgba(239, 68, 68, 0.2);" aria-label="Cerrar sesión segura">
            <h3 style="color: var(--danger);">🔒 Cerrar Sesión</h3>
            <p>Termina tu estado de sesión de forma segura en este navegador.</p>
        </a>

    </div>
  </section>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Módulo de Informática</p>
    <div class="footer-links">
        <a href="index.php">Inicio</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="dashboard.php">Mi Panel</a>
    </div>
</footer>
</body>
</html>