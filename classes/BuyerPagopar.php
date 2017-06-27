<?php
/**
 * Clase del Item de Pagopar
 * @author "Pagopar" <desarrollo@pagopar.com>
 * @version 1 4/5/2017
 */

class BuyerPagopar{

    public $name; //string (Obligatorio) Nombre del producto
    public $cityId; //int (Obligatorio) Id de la ciudad
    public $email; //string Email del Comprador
    public $tel; //string Teléfono del comprador
    public $typeDoc; //string Tipo de documento del comprador
    public $doc; //string Documento del comprador
    public $addr; //string Dirección del comprador
    public $addRef; //string Referencia de la dirección del comprador
    public $addrCoo; //string Coordenadas  (latitud y longitud separadas por coma) de la dirección del comprador
    public $ruc; //string RUC del comprador
    public $socialReason; //string Razón social del comprador

    /**
     * Constructor de la clase
     */
    public function __construct() {
    }

    /**
     * Devuelve el producto en forma de array
     * @return array Array del Producto
     */
    public function formatToArray(){
        return [
            'nombre' => $this->name,
            'ciudad' => $this->cityId,
            'email'  => $this->email,
            'telefono' => $this->tel,
            'tipo_documento' => $this->typeDoc,
            'documento' => $this->doc,
            'direccion' => $this->addr,
            'direccion_referencia' => $this->addRef,
            'coordenadas' => $this->addrCoo,
            'ruc' => $this->ruc,
            'razon_social' => $this->socialReason,
        ];
    }
}
