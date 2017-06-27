<?php
/**
 * Clase del Item de Pagopar
 * @author "Pagopar" <desarrollo@pagopar.com>
 * @version 1 4/5/2017
 */

class ItemPagopar{

    public $name; //string (Obligatorio) Nombre del producto
    public $qty; //int (Obligatorio) Cantidad de unidades del producto
    public $price; //int (Obligatorio) Suma total de los precios de los productos
    public $cityId; //int (Obligatorio) Id de la ciudad
    public $desc; //string Descripción del producto
    public $url_img; //string Url de la imagen del producto
    public $weight; //string Peso del producto
    public $category;//int Id de la Categoría
    public $productId;//int Id del producto (elegido por el usuario)
    public $large;//string Largo del producto
    public $width;//string Ancho del producto
    public $height;//string Alto del producto
    public $sellerPhone; //string Teléfono del vendedor
    public $sellerAddress; //string Dirección del vendedor
    public $sellerAddressRef; //string Referencia de la dirección del vendedor
    public $sellerAddressCoo; //string Coordenadas (latitud y longitud separados por coma) de la dirección del vendedor
    public $sellerPublicKey;//string Clave pública del vendedor

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
            'cantidad' => $this->qty,
            'precio_total' => $this->price,
            'ciudad' => $this->cityId,
            'descripcion' => $this->desc,
            'url_imagen' => $this->url_img,
            'peso' => $this->weight,
            'vendedor_telefono' => $this->sellerPhone,
            'vendedor_direccion' => $this->sellerAddress,
            'vendedor_direccion_referencia' => $this->sellerAddressRef,
            'vendedor_direccion_coordenadas' => $this->sellerAddressCoo,
            'public_key' => $this->sellerPublicKey,
            'categoria' => $this->category,
            'id_producto' => $this->productId,
            'largo' => $this->large,
            'ancho' => $this->width,
            'alto' => $this->height
        ];
    }
}