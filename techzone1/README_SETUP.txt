TECH ZONE (Proyecto escolar) - Instrucciones rápidas

1) Base de datos
- Importa database.sql en phpMyAdmin (crea la BD techzone y las tablas).

2) Configurar conexión
- conexion.php está configurado para:
  host: localhost
  user: root
  pass: (vacío)
  db: techzone
  Cambia esos datos si tu XAMPP/WAMP tiene otro usuario/clave.

3) Crear usuario admin
- Regístrate desde registro.php
- Luego en phpMyAdmin ejecuta:
  UPDATE usuarios SET rol='admin' WHERE usuario='TU_USUARIO';

4) Flujo
- login.php -> dashboard.php -> tienda.php
- admin.php solo lo ve un usuario con rol=admin

Notas
- CSS es intencionalmente sencillo.
- La compra (comprar.php) solo registra en la tabla compras (demo).


PRODUCTOS:
- Este proyecto incluye productos de ejemplo en database.sql.
- Si ya importaste la BD antes, vuelve a importar database.sql o ejecuta los INSERT al final del archivo.
