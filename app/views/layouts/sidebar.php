<?php
$rol     = $usuario['rol'] ?? 0;

$roles = [
    1 => 'Administrador',
    2 => 'Vendedor',
    3 => 'Clientes'
];

$nombreRol = $roles[$rol] ?? 'Desconocido';
$usuario = $usuario ?? [];
$nombreUsuario = $usuario['username'] ?? 'Usuario';
$current = trim($_GET['url'] ?? '', '/');

$palabras = explode(' ', $nombreUsuario);

$iniciales = '';
foreach ($palabras as $p) {
    $iniciales .= $p[0];
}

$iniciales = strtoupper(substr($iniciales, 0, 2));
?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <div class="sidebar-marca">
            <div class="marca-icono"><img src="<?= BASE_URL ?>images/Logo_VentaVista.png" alt="logo sistema"></div>
            <div>
                <div class="marca-texto">Venta Vista</div>
                <div class="marca-sub">Catálogo Pro</div>
            </div>
        </div>
    </div>

<nav class="sidebar-nav" id="sidebar-nav">
    <div class="nav-seccion">
      <div class="nav-seccion-titulo">Principal</div>

        <!-- Dashboard — solo Admin y Vendedor -->
        <?php if (in_array($rol, [1, 2])): ?>
        <a href="<?= BASE_URL ?>dashboard"
        class="nav-item <?= $current === 'dashboard' ? 'activo' : '' ?>">
        <img src="<?= BASE_URL ?>images/icons/dashboard-icon.png" class="icon" alt="logo sistema">
       <span>Dashboard</span>
     </a>
      <?php endif; ?>

        <!-- Solo Administrador (idRol = 1) y Vendedor (idRol = 2) -->
        <?php if (in_array($rol, [1, 2])): ?>

            <div class="nav-seccion">
                <div class="nav-seccion-titulo">Catálogo</div>

                <a href="<?= BASE_URL ?>productos" class="nav-item <?= str_starts_with($current, 'productos') ? 'activo' : '' ?>">
                    <img src="<?= BASE_URL ?>images/icons/catalogo-icon.png" class="icon" alt="logo sistema">
                    <span>Catálogo</span>
                </a>

                <a href="<?= BASE_URL ?>pedidos"
                   class="nav-item <?= str_starts_with($current, 'pedidos') ? 'activo' : '' ?>">
                    <img src="<?= BASE_URL ?>images/icons/pedido-icon.png" class="icon" alt="logo sistema">
                    <span>Pedidos</span>
                </a>
            </div>

            <div class="nav-seccion">
                <div class="nav-seccion-titulo">Administración</div>
                <?php if ($rol == 2): ?>
                    <a href="<?= BASE_URL ?>clientes" class="nav-item <?= str_starts_with($current, 'clientes') ? 'activo' : '' ?>">
                        <img src="<?= BASE_URL ?>images/icons/people-icon.png" class="icon" alt="logo sistema">
                        <span>Clientes</span>
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>inventario"
                   class="nav-item <?= str_starts_with($current, 'inventario') ? 'activo' : '' ?>">
                    <img src="<?= BASE_URL ?>images/icons/inventario-icon.png" class="icon" alt="logo sistema">
                    <span>Inventario</span>
                </a>
                <a href="<?= BASE_URL ?>cupones"
                   class="nav-item <?= str_starts_with($current, 'cupones') ? 'activo' : '' ?>">
                    <img src="<?= BASE_URL ?>images/icons/cupon-icon.png" class="icon" alt="logo sistema">
                    <span>Cupones</span>
                </a>
            </div>

        <?php endif; ?>

        <!-- Solo Administrador -->
        <?php if ($rol === 1): ?>

            <a href="<?= BASE_URL ?>usuarios"
               class="nav-item <?= str_starts_with($current, 'usuarios') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/people-icon.png" class="icon" alt="logo sistema">
                <span>Usuarios</span>
            </a>

            <a href="<?= BASE_URL ?>reportes"
               class="nav-item <?= str_starts_with($current, 'reportes') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/reportes-icon.png" class="icon" alt="logo sistema">
                <span>Reportes</span>
            </a>

        <?php endif; ?>

        <!-- Configuración — Admin y Vendedor -->
        <?php if (in_array($rol, [1, 2])): ?>
            <a href="<?= BASE_URL ?>configuracion"
               class="nav-item <?= str_starts_with($current, 'configuracion') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/configuracion-icon.png" class="icon" alt="logo sistema">
                <span>Configuración</span>
            </a>
        <?php endif; ?>

        <!-- Cliente (idRol = 3) -->
        <?php if ($rol == 3): ?>

            <div class="nav-seccion-titulo">Mi Cuenta</div>

            <a href="<?= BASE_URL ?>productos" class="nav-item <?= str_starts_with($current, 'productos') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/catalogo-icon.png" class="icon" alt="logo sistema">
                <span>Catálogo</span>
            </a>
            <a href="<?= BASE_URL ?>carrito"
               class="nav-item <?= str_starts_with($current, 'carrito') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/carrito-icon.png" class="icon" alt="logo sistema">
                <span>Carrito</span>
            </a>
            <a href="<?= BASE_URL ?>pedidos"
               class="nav-item <?= str_starts_with($current, 'pedidos') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/pedido-icon.png" class="icon" alt="logo sistema">
                <span>Mis Pedidos</span>
            </a>
            <a href="<?= BASE_URL ?>configuracion"
               class="nav-item <?= str_starts_with($current, 'configuracion') ? 'activo' : '' ?>">
                <img src="<?= BASE_URL ?>images/icons/configuracion-icon.png" class="icon" alt="logo sistema">
                <span>Mi Cuenta</span>
            </a>

        <?php endif; ?>

    </div>
</nav>

<div class="sidebar-footer">
    <div class="sidebar-logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"></path>
        </svg>
        <a href="<?= BASE_URL ?>logout">Cerrar Sesión</a>
    </div>
</div>

</aside>
