<?php
/**
 * Clase del pedido de Pagopar
 * @author "Pagopar" <desarrollo@pagopar.com>
 * @version 1 3/5/2017
 */

require_once 'ItemPagopar.php';
require_once 'BuyerPagopar.php';

class OrderPagopar{
    public $order = [];

    public $idOrder;
    public $publicKey;
    public $privateKey;
    public $typeOrder;
    public $desc;
    public $periodOfDaysForPayment = null;
    public $periodOfHoursForPayment = null;

    /**
     * Constructor de la clase
     */
    public function __construct($id){
        $this->idOrder = $id;
        $this->order = [
            'tipo_pedido' => null,
            'fecha_maxima_pago' => null,
            'public_key' => null,
            'id_pedido_comercio' => null,
            'monto_total' => null,
            'token' => null,
            'descripcion_resumen' => null,
            'comprador' => null,
            'compras_items' => null
        ];
    }

    /**
     * Agrega un producto (item) al pedido
     * @param object $item (Obligatorio) Producto
     * @throws Exception
     */
    public function addPagoparItem($item){
        $error = $this->validateItemAttributes($item->name, $item->qty, $item->price, $item->cityId);
        if($error['status']){
            throw new Exception($error['msg']);
        }
        $this->order['compras_items'][] = $item->formatToArray();
    }

    /**
     * Valida los parámetros pasados al contructor del producto
     * @param string $name (Obligatorio) Nombre del producto
     * @param int $qty (Obligatorio) Cantidad de unidades del producto
     * @param int $price (Obligatorio) Suma total de los precios de los productos
     * @param int $cityId (Obligatorio) Id de la ciudad
     * @return array $error Array con el status (true|false) y el mensaje de error
     */
    private function validateItemAttributes($name, $qty, $price, $cityId){
        $error = ['status'=>false,'msg'=>''];
        if(empty($name)){
            $error['status'] = true; $error['msg'] = "Hay un error con el nombre de algún producto";
            return $error;
        }
        if(empty($qty) || !is_numeric($qty) || $qty < 0){
            $error['status'] = true; $error['msg'] = "Hay un error en la cantidad del producto con nombre '{$name}'";
            return $error;
        }
        if(empty($price) || !is_numeric($price) || $price < 0){
            $error['status'] = true; $error['msg'] = "Hay un error en el precio del producto con nombre '{$name}'";
            return $error;
        }
        if(empty($cityId) || !is_numeric($cityId) || $cityId < 0){
            $error['status'] = true; $error['msg'] = "Hay un error en el ID de la ciudad del producto con nombre '{$name}'";
            return $error;
        }
        return $error;
    }

    /**
     * Agrega un comprador al pedido

     * @throws Exception
     */
    public function addPagoparBuyer($buyer){
        $error = $this->validateBuyerAttributes($buyer->name,$buyer->email,$buyer->cityId);
        if($error['status']){
            throw new Exception($error['msg']);
        }

        $this->order['comprador'] = $buyer->formatToArray();
    }

    /**
     * Valida los parámetros del comprador pasados a addPagoparBuyer
     * @param string $name (Obligatorio) Nombre del producto
     * @param string $email (Obligatorio) Email del comprador
     * @param int $cityId (Obligatorio) Id de la ciudad
     * @return array $error Array con el status (true|false) y el mensaje de error
     */
    private function validateBuyerAttributes($name,$email,$cityId){
        $error = ['status'=>false,'msg'=>''];
        if(empty($name)){
            $error['status'] = true; $error['msg'] = "Hay un error con el nombre del comprador";
            return $error;
        }
        if(empty($email)){
            $error['status'] = true; $error['msg'] = "Hay un error con el email del comprador";
            return $error;
        }
        if(empty($cityId) || !is_numeric($cityId) || $cityId < 0){
            $error['status'] = true; $error['msg'] = "Hay un error en el ID de la ciudad del comprador";
            return $error;
        }
        return $error;
    }

    /**
     * Valida los parámetros del pedido pasados a makeOrder
     * @param string $publicKey (Obligatorio) Clave pública del comercio
     * @param string $privateKey (Obligatorio) Clave privada del comercio
     * @param int $typeOrder (Obligatorio) Tipo de pedido
     * @return array $error Array con el status (true|false) y el mensaje de error
     */
    public function validateOrderAttributes($publicKey,$privateKey,$typeOrder){
        $error = ['status'=>false,'msg'=>''];
        //Validamos los keys
        if(empty($publicKey)){
            $error['status'] = true;$error['msg'] = "Hay un error con la clave pública";
        }
        if(empty($privateKey)){
            $error['status'] = true;$error['msg'] = "Hay un error con la clave privada";
        }
        //Validamos el tipo de Pedido
        if(empty($typeOrder)){
            $error['status'] = true;$error['msg'] = "Hay un error con el tipo de Pedido";
        }
        return $error;
    }

    /**
     * Genera un hash del pedido
     * @param int $id Id del pedido
     * @param int $amount Monto total del pedido
     * @return string Hash del pedido
     */
    private function generateOrderHash($id = null, $amount = 0, $private_token = null){
        return sha1($private_token . $id . $amount);
    }

    /**
     * Genera la máxima fecha de pago
     * @return string Fecha en formato yyyy-mm-dd hh:mm:ss
     */
    private function makeMaxDateForPayment(){
        //Transformamos el día a horas
        $daysToHours = ($this->periodOfDaysForPayment)?($this->periodOfDaysForPayment*24):0;
        return date("Y-m-d H:i:s",mktime(date("h")+$this->periodOfHoursForPayment+$daysToHours,
            date("i"),date("s"),date("m"),date("d"),date("Y")));
    }

    /**
     * Obtiene el precio total de la compra
     * @return int Suma total del precio de los Items
     */
    private function getTotalAmount($items){
        $totalAmount = 0;
        foreach ($items as $item){
            $totalAmount += $item['precio_total'];
        }
        return $totalAmount;
    }

    /**
     * Genera el pedido
     * @param int $idOrder Id del pedido
     * @param string $publicKey (Obligatorio) Clave pública del comercio
     * @param string $privateKey (Obligatorio) Clave privada del comercio
     * @param int $typeOrder (Obligatorio) Tipo de pedido
     * @param string $desc Descripción del pedido
     * @param int $periodDays Periodo máximo de días para completar el pago
     * @param int $periodHours Periodo máximo de horas para completar el pago
     * @return array Array formado del pedido
     * @throws Exception
     */
    public function makeOrder(){
        //Validamos los periodos máximos de compra de días y horas.
        //1 día por default
        $this->periodOfDaysForPayment =
            ($this->periodOfDaysForPayment>=0 && is_numeric($this->periodOfDaysForPayment))?$this->periodOfDaysForPayment:1;
        //Solo días y no horas por default
        $this->periodOfHoursForPayment =
            ($this->periodOfHoursForPayment>=0 && is_numeric($this->periodOfHoursForPayment))?$this->periodOfHoursForPayment:0;

        $error = $this->validateOrderAttributes($this->publicKey,$this->privateKey,$this->typeOrder);
        if($error['status']){
            throw new Exception($error['msg']);
        }

        $totalAmount = $this->getTotalAmount($this->order['compras_items']);

        //Datos de configuración del pedido
        $this->order['public_key'] = $this->publicKey;
        $this->order['tipo_pedido'] = $this->typeOrder;
        $this->order['fecha_maxima_pago'] = $this->makeMaxDateForPayment();
        $this->order['id_pedido_comercio'] = $this->idOrder;
        $this->order['monto_total'] = $totalAmount;
        $this->order['token'] = $this->generateOrderHash($this->idOrder,$totalAmount,$this->privateKey);
        $this->order['descripcion_resumen'] = $this->desc;

        return $this->order;
    }
}