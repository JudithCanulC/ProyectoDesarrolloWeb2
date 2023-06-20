<?php

/**
 * Clase base para la representacion de las vistas
 */
abstract class VistaApi{

    // C�digo de error
    public $estado;

    public abstract function imprimir($cuerpo);
}