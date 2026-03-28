<?php

namespace app\models;

class ItemCarrito {

    public $producto;
    public $cantidad;
    public $precio;

    public function __construct($producto,$cantidad,$precio){

        $this->producto = $producto;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
    }

}