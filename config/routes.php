<?php

return [

    // ── Auth ────────────────────────────────────────────────────
    ''       => ['controller' => 'AuthController', 'action' => 'login'],
    'login'  => ['controller' => 'AuthController', 'action' => 'autenticar'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],

    // ── Dashboard ───────────────────────────────────────────────
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],

    // ── Productos ───────────────────────────────────────────────
    'productos'          => ['controller' => 'ProductoController', 'action' => 'index'],
    'productos/crear'    => ['controller' => 'ProductoController', 'action' => 'crear'],
    'productos/editar'   => ['controller' => 'ProductoController', 'action' => 'editar'],
    'productos/eliminar' => ['controller' => 'ProductoController', 'action' => 'eliminar'],
    'productos/ver'      => ['controller' => 'ProductoController', 'action' => 'ver'],

    // ── Clientes ────────────────────────────────────────────────
    'clientes'          => ['controller' => 'ClienteController', 'action' => 'index'],
    'clientes/crear'    => ['controller' => 'ClienteController', 'action' => 'crear'],
    'clientes/editar'   => ['controller' => 'ClienteController', 'action' => 'editar'],
    'clientes/ver'      => ['controller' => 'ClienteController', 'action' => 'ver'],
    'clientes/eliminar' => ['controller' => 'ClienteController', 'action' => 'eliminar'],

    // ── Pedidos ─────────────────────────────────────────────────
    'pedidos'          => ['controller' => 'PedidoController', 'action' => 'index'],
    'pedidos/crear'    => ['controller' => 'PedidoController', 'action' => 'crear'],
    'pedidos/ver'      => ['controller' => 'PedidoController', 'action' => 'ver'],
    'pedidos/estado'   => ['controller' => 'PedidoController', 'action' => 'estado'],
    'pedidos/eliminar' => ['controller' => 'PedidoController', 'action' => 'eliminar'],

    // ── Carrito ─────────────────────────────────────────────────
    'carrito'               => ['controller' => 'CarritoController', 'action' => 'index'],
    'carrito/agregar'       => ['controller' => 'CarritoController', 'action' => 'agregar'],
    'carrito/actualizar'    => ['controller' => 'CarritoController', 'action' => 'actualizar'],
    'carrito/eliminar-item' => ['controller' => 'CarritoController', 'action' => 'eliminarItem'],
    'carrito/vaciar'        => ['controller' => 'CarritoController', 'action' => 'vaciar'],

    // ── Cupones ─────────────────────────────────────────────────
    'cupones'          => ['controller' => 'CuponController', 'action' => 'index'],
    'cupones/crear'    => ['controller' => 'CuponController', 'action' => 'crear'],
    'cupones/editar'   => ['controller' => 'CuponController', 'action' => 'editar'],
    'cupones/eliminar' => ['controller' => 'CuponController', 'action' => 'eliminar'],
    'cupones/validar'  => ['controller' => 'CuponController', 'action' => 'validar'],

    // ── Pagos ───────────────────────────────────────────────────
    'pagos'        => ['controller' => 'PagoController', 'action' => 'index'],
    'pagos/crear'  => ['controller' => 'PagoController', 'action' => 'crear'],
    'pagos/estado' => ['controller' => 'PagoController', 'action' => 'estado'],

    // ── Envios ──────────────────────────────────────────────────
    'envios'           => ['controller' => 'EnvioController', 'action' => 'index'],
    'envios/crear'     => ['controller' => 'EnvioController', 'action' => 'crear'],
    'envios/entregar'  => ['controller' => 'EnvioController', 'action' => 'entregar'],
    'envios/rastreo'   => ['controller' => 'EnvioController', 'action' => 'rastreo'],

    // ── Usuarios ────────────────────────────────────────────────
    'usuarios'          => ['controller' => 'UsuarioController', 'action' => 'index'],
    'usuarios/crear'    => ['controller' => 'UsuarioController', 'action' => 'crear'],
    'usuarios/estado'   => ['controller' => 'UsuarioController', 'action' => 'estado'],
    'usuarios/password' => ['controller' => 'UsuarioController', 'action' => 'password'],
    'usuarios/eliminar' => ['controller' => 'UsuarioController', 'action' => 'eliminar'],

    // ── Reportes ────────────────────────────────────────────────
    'reportes'           => ['controller' => 'ReporteController', 'action' => 'index'],
    'reportes/ventas'    => ['controller' => 'ReporteController', 'action' => 'ventas'],
    'reportes/productos' => ['controller' => 'ReporteController', 'action' => 'productos'],
    'reportes/clientes'  => ['controller' => 'ReporteController', 'action' => 'clientes'],

];