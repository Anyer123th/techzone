// main.js - Manejo dinámico de navegación y protección de rutas para TechZone

document.addEventListener('DOMContentLoaded', () => {
    // Verificar si db.js cargó correctamente
    if (!window.db) {
        console.error('El motor de simulación db.js no está cargado.');
        return;
    }

    const user = window.db.getCurrentUser();
    
    // 1. CONTROL DE PROTECCIÓN DE RUTAS
    const requiresAuth = document.body.hasAttribute('data-require-auth');
    const requiresAdmin = document.body.hasAttribute('data-require-admin');
    const requiresGuest = document.body.hasAttribute('data-require-guest');
    
    if (requiresAuth && !user) {
        window.location.href = 'login.html';
        return;
    }
    if (requiresAdmin && (!user || user.rol !== 'admin')) {
        window.location.href = '403.html';
        return;
    }
    if (requiresGuest && user) {
        window.location.href = 'dashboard.html';
        return;
    }

    // 2. ACTUALIZACIÓN DINÁMICA DEL MENÚ DE NAVEGACIÓN
    const nav = document.getElementById('main-nav');
    if (nav) {
        const activePage = nav.getAttribute('data-active') || '';
        
        const isInicioActive = activePage === 'inicio' ? 'aria-current="page"' : '';
        const isContactoActive = activePage === 'contacto' ? 'aria-current="page"' : '';
        const isTiendaActive = activePage === 'tienda' ? 'aria-current="page"' : '';
        const isDashboardActive = activePage === 'dashboard' ? 'aria-current="page"' : '';
        const isAdminActive = activePage === 'admin' ? 'aria-current="page"' : '';

        let navHtml = `
            <a href="index.html" ${isInicioActive}>Inicio</a>
            <a href="contacto.html" ${isContactoActive}>Contacto</a>
            <a href="tienda.html" ${isTiendaActive}>Tienda/Catalogo</a>
        `;
        
        if (user) {
            navHtml += `
                <a href="dashboard.html" ${isDashboardActive}>Mi Panel</a>
            `;
            if (user.rol === 'admin') {
                navHtml += `
                    <a href="admin.html" style="color: #3b82f6 !important;" ${isAdminActive}>Admin Panel</a>
                `;
            }
            navHtml += `
                <a href="#" id="logout-btn" style="color: var(--danger) !important;">Salir</a>
            `;
        } else {
            navHtml += `
                <a href="login.html" class="btn-nav">Iniciar Sesión</a>
            `;
        }
        
        nav.innerHTML = navHtml;

        // Listener para cerrar sesión
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.db.logout();
                window.location.href = 'index.html';
            });
        }
    }
});
