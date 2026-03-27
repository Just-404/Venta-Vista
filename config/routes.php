<?php

return [

    '' => ['controller' => 'AuthController', 
            'action' => 'login'],

    'login' => ['controller' => 'AuthController', 
                'action' => 'autenticar'],

    'logout' => ['controller' => 'AuthController', 
                'action' => 'logout'],

    'dashboard' => ['controller' => 'DashboardController',
                    'action' => 'index'],

    'productos' => ['controller' => 'ProductoController', 'action' => 'index'],
    'productos/crear' => ['controller' => 'ProductoController', 'action' => 'crear'],
    'productos/editar' => ['controller' => 'ProductoController', 'action' => 'editar'],

    'clientes' => ['controller' => 'ClienteController', 'action' => 'index'],

    'pedidos' => ['controller' => 'PedidoController', 'action' => 'index'],

];