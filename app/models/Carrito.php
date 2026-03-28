<?php

namespace app\models;

class Carrito {

    public static function obtener(){

        return $_SESSION['carrito'] ?? [];
    }

    public static function agregar($producto){

        $_SESSION['carrito'][] = $producto;
    }

    public static function limpiar(){

        unset($_SESSION['carrito']);
    }

}