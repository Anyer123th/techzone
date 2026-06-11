// db.js - Base de datos simulada en localStorage para TechZone

(function() {
    // Inicializar localStorage si no existe
    function init() {
        if (!localStorage.getItem('techzone_initialized')) {
            // Usuarios por defecto (contraseña en texto plano para el demo y validación simple)
            const defaultUsers = [
                { id: 1, usuario: 'admin', clave: 'admin', rol: 'admin', fecha_registro: new Date().toISOString().replace('T', ' ').substring(0, 19) },
                { id: 2, usuario: 'user', clave: 'user', rol: 'user', fecha_registro: new Date().toISOString().replace('T', ' ').substring(0, 19) }
            ];

            // Productos por defecto (datos originales de database.sql)
            const defaultProducts = [
                { id: 1, nombre: 'Laptop', precio: 45000.00, imagen: 'laptop.jpg', descripcion: 'Laptop de alto rendimiento ideal para desarrollo, simulación y hardware de alta fidelidad. Equipada con procesador potente y gráficos de vanguardia.' },
                { id: 2, nombre: 'Mouse', precio: 850.00, imagen: 'mouse.jpg', descripcion: 'Mouse óptico de alta precisión con diseño ergonómico para soportar largas jornadas de SimRacing o programación.' },
                { id: 3, nombre: 'Teclado', precio: 1200.00, imagen: 'teclado.png', descripcion: 'Teclado mecánico retroiluminado con switches de alta respuesta para una precisión óptima.' },
                { id: 4, nombre: 'Monitor', precio: 9500.00, imagen: 'monitor.jpg', descripcion: 'Monitor curvo gaming de 27 pulgadas, frecuencia de actualización de 144Hz para máxima inmersión.' },
                { id: 5, nombre: 'Audífonos', precio: 1800.00, imagen: 'Aud#U00edfonos.png', descripcion: 'Auriculares premium con sonido envolvente y cancelación de ruido pasiva avanzada.' },
                { id: 6, nombre: 'Bocina', precio: 2200.00, imagen: 'Bocina.png', descripcion: 'Bocina corporativa inalámbrica con graves profundos y autonomía extendida.' },
                { id: 7, nombre: 'Cámara', precio: 15500.00, imagen: 'camara.png', descripcion: 'Cámara HD ideal para retransmisión de carreras virtuales u videoconferencias ejecutivas.' },
                { id: 8, nombre: 'Router WiFi', precio: 2100.00, imagen: 'router.jpg', descripcion: 'Router de banda ancha inteligente para garantizar la menor latencia posible en red.' },
                { id: 9, nombre: 'Memoria USB', precio: 450.00, imagen: 'Memoria USB.png', descripcion: 'Memoria USB 3.2 de almacenamiento robusto para resguardar configuraciones y perfiles.' },
                { id: 10, nombre: 'Disco duro', precio: 3200.00, imagen: 'Disco duro.png', descripcion: 'Unidad externa portátil de 2TB con alta resistencia mecánica y tasa de transferencia elevada.' },
                { id: 11, nombre: 'Webcam', precio: 1700.00, imagen: 'webcam.jpg', descripcion: 'Cámara para streaming de alta definición con enfoque automático e iluminación inteligente integrada.' },
                { id: 12, nombre: 'Impresora', precio: 12500.00, imagen: 'Impresora.png', descripcion: 'Impresora multifunción inalámbrica con sistema continuo ecológico de alta eficiencia.' }
            ];

            const defaultCompras = [
                { id: 1, usuario_id: 2, producto_id: 1, fecha: new Date().toISOString().replace('T', ' ').substring(0, 19) }
            ];

            const defaultTickets = [
                { id: 1, cliente: 'user', asunto: 'Ayuda con calibración de monitor', mensaje: 'Hola, compré el monitor de 144Hz pero solo me muestra 60Hz en las opciones de Windows.', fecha_creacion: new Date().toISOString().replace('T', ' ').substring(0, 19) }
            ];

            localStorage.setItem('techzone_users', JSON.stringify(defaultUsers));
            localStorage.setItem('techzone_products', JSON.stringify(defaultProducts));
            localStorage.setItem('techzone_compras', JSON.stringify(defaultCompras));
            localStorage.setItem('techzone_tickets', JSON.stringify(defaultTickets));
            localStorage.setItem('techzone_initialized', 'true');
        }
    }

    init();

    // Helper para obtener y guardar datos en localStorage
    function get(key) {
        return JSON.parse(localStorage.getItem(key)) || [];
    }

    function set(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }

    // API de la base de datos simulada
    window.db = {
        // --- SECCIÓN DE USUARIOS ---
        getUsers: function() {
            return get('techzone_users');
        },
        
        getUserById: function(id) {
            return this.getUsers().find(u => u.id === parseInt(id));
        },
        
        registerUser: function(usuario, password) {
            const users = this.getUsers();
            if (users.some(u => u.usuario.toLowerCase() === usuario.toLowerCase())) {
                return false; // Nombre de usuario ya tomado
            }
            const newUser = {
                id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
                usuario: usuario,
                clave: password, // Almacenado en plano para la simulación
                rol: 'user',
                fecha_registro: new Date().toISOString().replace('T', ' ').substring(0, 19)
            };
            users.push(newUser);
            set('techzone_users', users);
            return newUser;
        },

        updateUserPassword: function(userId, newPassword) {
            const users = this.getUsers();
            const userIndex = users.findIndex(u => u.id === parseInt(userId));
            if (userIndex !== -1) {
                users[userIndex].clave = newPassword;
                set('techzone_users', users);
                
                // Si el usuario editado es el actual, actualiza la sesión
                const currentUser = this.getCurrentUser();
                if (currentUser && currentUser.id === parseInt(userId)) {
                    currentUser.clave = newPassword;
                    sessionStorage.setItem('techzone_session', JSON.stringify(currentUser));
                }
                return true;
            }
            return false;
        },

        validateLogin: function(usuario, password) {
            const users = this.getUsers();
            const user = users.find(u => u.usuario === usuario && u.clave === password);
            if (user) {
                sessionStorage.setItem('techzone_session', JSON.stringify(user));
                return user;
            }
            return null;
        },

        getCurrentUser: function() {
            return JSON.parse(sessionStorage.getItem('techzone_session')) || null;
        },

        logout: function() {
            sessionStorage.removeItem('techzone_session');
        },

        // --- SECCIÓN DE PRODUCTOS ---
        getProducts: function(buscar = '', orden = 'recientes') {
            let products = get('techzone_products');
            
            // Filtrar
            if (buscar.trim() !== '') {
                const term = buscar.toLowerCase();
                products = products.filter(p => p.nombre.toLowerCase().includes(term));
            }
            
            // Ordenar
            if (orden === 'precio_asc') {
                products.sort((a, b) => a.precio - b.precio);
            } else if (orden === 'precio_desc') {
                products.sort((a, b) => b.precio - a.precio);
            } else if (orden === 'nombre_asc') {
                products.sort((a, b) => a.nombre.localeCompare(b.nombre));
            } else { // 'recientes' / id descendente
                products.sort((a, b) => b.id - a.id);
            }
            
            return products;
        },

        getProductById: function(id) {
            return this.getProducts().find(p => p.id === parseInt(id));
        },

        addProduct: function(nombre, precio, descripcion, imagen) {
            const products = get('techzone_products');
            const newProduct = {
                id: products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1,
                nombre: nombre,
                precio: parseFloat(precio),
                descripcion: descripcion,
                imagen: imagen || 'monitor.png'
            };
            products.push(newProduct);
            set('techzone_products', products);
            return true;
        },

        deleteProduct: function(id) {
            let products = get('techzone_products');
            products = products.filter(p => p.id !== parseInt(id));
            set('techzone_products', products);
            return true;
        },

        // --- SECCIÓN DE COMPRAS ---
        getCompras: function(usuarioId = null) {
            const compras = get('techzone_compras');
            const products = get('techzone_products');
            const users = get('techzone_users');
            
            // Unir compras con productos y usuarios
            let results = compras.map(c => {
                const p = products.find(prod => prod.id === c.producto_id) || { nombre: 'Hardware Eliminado', precio: 0.00, imagen: 'monitor.png' };
                const u = users.find(user => user.id === c.usuario_id) || { usuario: 'Invitado' };
                return {
                    id: c.id,
                    usuario_id: c.usuario_id,
                    usuario: u.usuario,
                    producto_id: c.producto_id,
                    producto_nombre: p.nombre,
                    producto_precio: p.precio,
                    producto_imagen: p.imagen,
                    fecha: c.fecha
                };
            });

            if (usuarioId !== null) {
                results = results.filter(r => r.usuario_id === parseInt(usuarioId));
            }

            // Ordenar por más reciente primero
            results.sort((a, b) => b.id - a.id);
            return results;
        },

        addCompra: function(usuarioId, productoId) {
            const compras = get('techzone_compras');
            const newCompra = {
                id: compras.length > 0 ? Math.max(...compras.map(c => c.id)) + 1 : 1,
                usuario_id: parseInt(usuarioId),
                producto_id: parseInt(productoId),
                fecha: new Date().toISOString().replace('T', ' ').substring(0, 19)
            };
            compras.push(newCompra);
            set('techzone_compras', compras);
            return newCompra;
        },

        // --- SECCIÓN DE TICKETS ---
        getTickets: function() {
            const tickets = get('techzone_tickets');
            tickets.sort((a, b) => b.id - a.id);
            return tickets;
        },

        addTicket: function(cliente, asunto, mensaje) {
            const tickets = get('techzone_tickets');
            const newTicket = {
                id: tickets.length > 0 ? Math.max(...tickets.map(t => t.id)) + 1 : 1,
                cliente: cliente,
                asunto: asunto,
                mensaje: mensaje,
                fecha_creacion: new Date().toISOString().replace('T', ' ').substring(0, 19)
            };
            tickets.push(newTicket);
            set('techzone_tickets', tickets);
            return true;
        }
    };
})();
