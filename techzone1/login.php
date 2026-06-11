<?php
session_start();
if(isset($_SESSION["usuario"])) {
    header("Location: dashboard.php");
    exit;
}

$error_msg = "";
$success_msg = "";

if (isset($_GET["error"])) {
    $error_msg = "Error: Credenciales incorrectas. Verifica tu usuario y contraseña.";
}
if (isset($_GET["registered"])) {
    $success_msg = "¡Cuenta creada con éxito! Ya puedes iniciar sesión en el servidor.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso al Sistema - TechZone</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    /* Estilos UX Premium para la pantalla de Login */
    .login-box {
        max-width: 450px;
        margin: 4rem auto;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    
    .password-container {
        position: relative;
    }

    .btn-toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        width: auto;
        padding: 0;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .btn-toggle-password:hover {
        color: #ffffff;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        text-align: center;
        font-weight: 600;
    }

    .alert-danger {
        background-color: rgba(239, 68, 68, 0.15);
        border: 1px solid var(--danger);
        color: #ff6b6b;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.15);
        border: 1px solid var(--success);
        color: #4ade80;
    }

    .redirect-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    .redirect-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 700;
    }
    
    .redirect-link a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <a href="tienda.php" class="brand">Tech<span>Zone</span></a>
  <nav aria-label="Menú Principal">
    <a href="tienda.php">Inicio / Tienda</a>
    <a href="contacto.php">Contacto</a>
    <a href="registro.php" class="btn-nav">Crear Cuenta</a>
  </nav>
</header>

<main class="container">
  <div class="login-box">
    <h1 style="text-align: center; margin-bottom: 0.5rem;">Iniciar Sesión</h1>
    <p class="muted" style="text-align: center; margin-bottom: 2rem;">Ingresa al ecosistema de hardware para gestionar tus compras.</p>

    <?php if($error_msg !== ""): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if($success_msg !== ""): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form action="validar_login.php" method="POST">
      
      <div class="form-group">
        <label for="usuario">Nombre de Usuario:</label>
        <input 
            type="text" 
            id="usuario" 
            name="usuario" 
            required 
            placeholder="Ej: anyer_peralta"
            autocomplete="username"
        >
      </div>

      <div class="form-group">
        <label for="password">Contraseña:</label>
        <div class="password-container">
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="Escribe tu contraseña"
                autocomplete="current-password"
            >
            <button type="button" id="btnToggle" class="btn-toggle-password" aria-label="Mostrar u ocultar contraseña">👁️ Ver</button>
        </div>
      </div>

      <button type="submit" style="margin-top: 1rem; font-weight: 700;">Ingresar de Forma Segura</button>
    </form>

    <div class="redirect-link">
        ¿Aún no tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
  </div>
</main>

<footer>
    <p>&copy; 2026 TechZone SRL. Todos los derechos reservados.</p>
    <p>Santiago de los Caballeros, República Dominicana | Técnico en Informática</p>
    <div class="footer-links">
        <a href="tienda.php">Inicio / Tienda</a>
        <a href="contacto.php">Contacto y Soporte</a>
        <a href="registro.php">Registro de Cuentas</a>
    </div>
</footer>

<script>
    const btnToggle = document.querySelector('#btnToggle');
    const passwordInput = document.querySelector('#password');

    btnToggle.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        this.textContent = isPassword ? '🔒 Ocultar' : '👁️ Ver';
    });
</script>
</body>
</html>