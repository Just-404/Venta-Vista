<?php
$rol     = $usuario['rol'] ?? 0;

$roles = [
    1 => 'Administrador',
    2 => 'Vendedor',
    3 => 'Clientes'
];

$nombreRol = $roles[$rol] ?? 'Desconocido';

$nombreUsuario = $usuario['username'] ?? '';
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
            <div class="marca-icono">Logo</div>
            <div>
                <div class="marca-texto">Venta Vista</div>
                <div class="marca-sub">Catálogo Pro</div>
            </div>
        </div>
    </div>

    <div class="sidebar-usuario">
        <div class="usuario-avatar" id="sb-avatar"><?= $iniciales ?></div>
        <div>
          <div class="usuario-nombre" id="sb-nombre"><?= $nombreUsuario ?></div>
          <div class="usuario-rol" id="sb-rol"><?= $nombreRol ?></div>
        </div>
      </div>

<nav class="sidebar-nav" id="sidebar-nav">
    <div class="nav-seccion">
      <div class="nav-seccion-titulo">Principal</div>

        <!-- Dashboard — todos los roles -->
        <a href="<?= BASE_URL ?>dashboard"
           class="nav-item <?= $current === 'dashboard' ? 'activo' : '' ?>">
            <span>📊</span>
            <span>Dashboard</span>
        </a>
    </div>
    
<!-- Solo Administrador (idRol = 1) y Vendedor (idRol = 2) -->
        <?php if (in_array($rol, [1, 2])): ?>

            <div class="nav-seccion">
                <div class="nav-seccion-titulo">Catálogo</div>

            <a href="<?= BASE_URL ?>productos" class="nav-item <?= str_starts_with($current, 'productos') ? 'activo' : '' ?>">
                <span>🏷️</span>
                <span>Catálogo</span>
            </a>
            
            <a href="<?= BASE_URL ?>pedidos"
               class="nav-item <?= str_starts_with($current, 'pedidos') ? 'activo' : '' ?>">
                <span>📦</span>
                <span>Pedidos</span>
            </a>
        </div>

        <div class="nav-seccion">
            <div class="nav-seccion-titulo">Administración</div>
            <a href="<?= BASE_URL ?>clientes" class="nav-item <?= str_starts_with($current, 'clientes') ? 'activo' : '' ?>">
                <span>👥</span>
                <span>Clientes</span>
            </a>
            <a href="<?= BASE_URL ?>inventario"
               class="nav-item <?= str_starts_with($current, 'inventario') ? 'activo' : '' ?>">
                <span>📋</span>
                <span>Inventario</span>
            </a> 
            <a href="<?= BASE_URL ?>cupones"
               class="nav-item <?= str_starts_with($current, 'cupones') ? 'activo' : '' ?>">
                <span>🎟️</span>
                <span>Cupones</span>
            </a> 
            
        </div>

        <?php endif; ?>

        <!-- Solo Administrador -->
        <?php if ($rol === 1): ?>

            <a href="<?= BASE_URL ?>usuarios"
               class="nav-item <?= str_starts_with($current, 'usuarios') ? 'activo' : '' ?>">
                <span>👥</span>
                <span>Usuarios</span>
            </a>

            <a href="<?= BASE_URL ?>reportes"
               class="nav-item <?= str_starts_with($current, 'reportes') ? 'activo' : '' ?>">
                <span>📈</span>
                <span>Reportes</span>
            </a>
            
            <a href="<?= BASE_URL ?>configuracion"
               class="nav-item <?= str_starts_with($current, 'configuracion') ? 'activo' : '' ?>">
                <span>⚙️</span>
                <span>Configuración</span>
            </a>

        <?php endif; ?>
        
         <!-- Cliente (idRol = 3) -->
        <?php if ($rol === 3): ?>

            <div class="nav-section">Mi cuenta</div>

            <a href="<?= BASE_URL ?>productos" class="nav-item <?= str_starts_with($current, 'productos') ? 'activo' : '' ?>">
                <span>🏷️</span>
                <span>Catálogo</span>
            </a>
            <a href="<?= BASE_URL ?>carrito"
               class="nav-item <?= str_starts_with($current, 'carrito') ? 'activo' : '' ?>">
                <span>🛒</span>
                <span>Carrito</span>
            </a>

            <a href="<?= BASE_URL ?>pedidos"
               class="nav-item <?= str_starts_with($current, 'pedidos') ? 'activo' : '' ?>">
                <span>📦</span>
                <span>Mis Pedidos</span>
            </a>

        <?php endif; ?>

        </nav>
<div class="sidebar-footer">
        <div class="sidebar-logout" onclick="cerrarSesion()">
          <!-- Ícono de salida SVG inline -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"></path>
          </svg>
          <a href="<?= BASE_URL ?>logout">Cerrar Sesión</a>
        </div>
      </div>
</aside>
