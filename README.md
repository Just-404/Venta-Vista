# Venta-Vista
Proyecto de sistema de ventas por catálogo para la asignatura INF-5220

Requisitos

1. XAMPP (o cualquier servidor con Apache + PHP 8.1+ + MySQL 8.0+)
2. Módulo mod_rewrite habilitado en Apache
3. Tener Composer descargado. Si no lo tienen ir a https://getcomposer.org/

Instalación:
1. Clonar el repositorio: git clone https://github.com/tu-usuario/ventas_catalogo.git
2. Colocar la carpeta en C:/xampp/htdocs/ventas_catalogo luego usar el siguiente comando para instalar todas las dependencias: 
*composer install*

3. Crear la base de datos

-Abrir phpMyAdmin en http://localhost/phpmyadmin
-Crear una base de datos llamada ventas_catalogo
-Importar el archivo sql/ventas_catalogo.sql


5. Configurar la conexión
Abrir .env y ajustar los valores según tu entorno:

DB_HOST=localhost
DB_NAME=ventas_catalogo
DB_USER=root
DB_PASS=
CHARSET=utf8mb4
BASE_URL=http://localhost/ventas_catalogo/public

Estructura del proyecto
ventas_catalogo/
├── app/
│   ├── controllers/        # Controladores MVC
│   ├── core/               # Router, Controller base, Model base, Database
│   ├── models/             # Modelos de cada entidad
│   └── views/              # Vistas PHP
│       └── layout/         # Header, sidebar, footer y layout principal
├── config/
│   ├── config.php          # Configuración general y constantes
│   └── routes.php          # Mapa de rutas URL → Controller@acción
├── sql/
│   ├── ventas_catalogo.sql          # Estructura completa de la BD
│   
├── public/                 # Document root de Apache
│   ├── css/
│   ├── js/
│   ├── index.php           # Punto de entrada
│   └── .htaccess           # Para cambiar las reglas
└── README.md

Credenciales de prueba
Rol         Usuario         Contraseña
Administrador admin         Admin2026!
Vendedor v  endedor1        Test2026! 
Vendedor     vendedor2      Test2026!
Cliente     cliente1        Test2026!
Cliente     cliente2        Test2026!

Solución de problemas frecuentes:

1. SI EL CSS NO CARGA:
Verificar que BASE_URL en config.php termine con / y que los archivos estén en public/css/.

2. Redirige siempre al login
Verificar que session_start() esté en public/index.php y que el hash de la contraseña en la BD no tenga espacios. 
Ejecutar en phpMyAdmin:
sqlUPDATE usuarios SET contrasena = TRIM(contrasena);

3. Error de conexión a la BD
Verificar usuario, contraseña y nombre de la base de datos en config/config.php mediante .env.