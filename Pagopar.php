<?php
/**
 * Archivo SDK de Pagopar
 * @author "Pagopar" <desarrollo@pagopar.com>
 * @version 1 27/4/2017
 */

require_once 'lib/DBPagopar.php';

require_once 'classes/OrderPagopar.php';

class Pagopar{
    //Tokens del comercio //TODO(Quitar después)
    const TOKEN_PRIVADO = 'dflghdf5458';
    const TOKEN_PUBLICO = '123456abcdegf';
    //URLs de configuración
    const URL_BASE = 'https://api.pagopar.com/api/';
    const URL_COMERCIOS = self::URL_BASE.'comercios/1.1/iniciar-transaccion';
    const URL_PEDIDOS = self::URL_BASE.'pedidos/1.1/traer';
    const URL_REDIRECT = 'https://pagopar.com/pagos/%s';

    //Tipos de Tokens generados
    const TOKEN_TIPO_CONSULTA = 'CONSULTA';
    const TOKEN_TIPO_CIUDAD = 'CIUDADES';
    const TOKEN_TIPO_CATEGORIA = 'CATEGORIA';
    const TOKEN_TIPO_FLETE = 'CALCULAR-FLETE';

    //Base de datos
    protected $db;

    //Datos del pedido del comercio
    private $idOrder;
    private $hashOrder;

    public $order;

    /**
     * Constructor de la clase
     * @param int $id Id del pedido
     * @param $db
     * @internal param Database $PDO $db Base de Datos (Basada en PDO)
     */
    public function __construct($id = null,$db) {
        $this->db = $db;
        $this->idOrder = $id;
        $this->order = new OrderPagopar($id);
    }

    /**
     * Invoca a la URL de acuerdo a los parámetros
     * @param array $args Parámetros
     * @param  string $url Url a invocar
     * @return string Respuesta en formato JSON
     */
    private function runCurl($args, $url){
        $args = json_encode($args);

        $ch = curl_init();
        $headers= array('Accept: application/json','Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Inicia la transacción con Pagopar y si tiene éxito al generar el pedido,
     * redirecciona a la página de pago de Pagopar.
     * @throws Exception
     */
    public function newPagoparTransaction(){
        $orderPagopar = $this->order->makeOrder($this->idOrder);

        $response = $this->runCurl($orderPagopar, self::URL_COMERCIOS);
        $arrayResponse = json_decode($response);

        //Verificar si hay error
        if(!$arrayResponse->respuesta){
            throw new Exception($arrayResponse->resultado);
        }

        $this->hashOrder = $arrayResponse->resultado[0]->data;

        $this->db->insertTransaction($orderPagopar['id_pedido_comercio'],
            $orderPagopar['tipo_pedido'],
            $orderPagopar['monto_total'],
            $this->hashOrder,
            $orderPagopar['fecha_maxima_pago'],
            $orderPagopar['descripcion_resumen']
        );

        $this->redirectToPagopar($this->hashOrder);
    }

    /**
     * Redirecciona a la página de Pagopar
     * @param string $hash Hash del pedido
     */
    public function redirectToPagopar($hash){
        $url = sprintf(self::URL_REDIRECT, $hash);
        //Redireccionamos a Pagopar
        header('Location: '. $url);
        exit();
    }

    /**
     * Inicia la transacción con Pagopar y si tiene éxito al generar el pedido con valores de prueba,
     * redirecciona a la página de pago de Pagopar.
     */
    public function newTestPagoparTransaction(){
        //Creamos el comprador
        $buyer = new BuyerPagopar();
        $buyer->name            = 'Juan Perez';
        $buyer->email           = 'mihares@gmail.com';
        $buyer->cityId          = 1;
        $buyer->tel             = '0972200046';
        $buyer->typeDoc         = 'CI';
        $buyer->doc             = '352221';
        $buyer->addr            = 'Mexico 840';
        $buyer->addRef          = 'alado de credicentro';
        $buyer->addrCoo         = '-25.2844638,-57.6480038';
        $buyer->ruc             = null;
        $buyer->socialReason    = null;

        //Agregamos el comprador
        $this->order->addPagoparBuyer($buyer);

        //Creamos los productos
        $item1 = new ItemPagopar();
        $item1->name                = "Válido 1 persona";
        $item1->qty                 = 1;
        $item1->price               = 1000;
        $item1->cityId              = 1;
        $item1->desc                = "producto";
        $item1->url_img             = "http://www.clipartkid.com/images/318/tickets-for-the-film-festival-are-for-the-two-day-event-admission-is-lPOEYl-clipart.png";
        $item1->weight              = '0.1';
        $item1->category            = 3;
        $item1->sellerPhone         = '0985885487';
        $item1->sellerEmail         = 'mihares@gmail.com';
        $item1->sellerAddress       = 'dr paiva ca cssssom gaa';
        $item1->sellerAddressRef    = '';
        $item1->sellerAddressCoo    = '-28.75438,-57.1580038';

        $item2 = new ItemPagopar();
        $item2->name                = "Heladera";
        $item2->qty                 = 1;
        $item2->price               = 785000;
        $item2->cityId              = 1;
        $item2->desc                = "producto";
        $item2->url_img             = "https://cdn1.hendyla.com/archivos/imagenes/2017/04/09/publicacion-564c19b86b235526160f43483c76a69ee1a85c96c976c33e3e21ce6a5f9009b9-234_A.jpg";
        $item2->weight              = '5.0';
        $item2->category            = 3;
        $item2->sellerPhone         = '0985885487';
        $item2->sellerEmail         = 'mihares@gmail.com';
        $item2->sellerAddress       = 'dr paiva ca cssssom gaa';
        $item2->sellerAddressRef    = '';
        $item2->sellerAddressCoo    = '-28.75438,-57.1580038';

        //Agregamos los productos al pedido
        $this->order->addPagoparItem($item1);
        $this->order->addPagoparItem($item2);

        $this->order->publicKey = self::TOKEN_PUBLICO;
        $this->order->privateKey = self::TOKEN_PRIVADO;
        $this->order->typeOrder = 'VENTA-COMERCIO';
        $this->order->desc = 'Entrada Retiro';
        $this->order->periodDays = 1;
        $this->order->periodDays = 0;

        $this->newPagoparTransaction();
    }

    /**
     * Obtiene un JSON con el estado del pedido
     * @param int $id Id del pedido
     * @throws Exception
     */
    public function getPagoparOrderStatus($id){
        $this->idOrder = $id;
        $orderData = $this->db->selectTransaction("id=$id");
        if($orderData){
            $this->hashOrder = $orderData['hash'];
        }else{
            throw new Exception("Hay un error con el hash");
        }
        $token = $this->generateToken(self::TOKEN_TIPO_CONSULTA);

        $args = ['hash_pedido'=>$this->hashOrder, 'token'=>$token, 'token_publico'=> self::TOKEN_PUBLICO];
        $arrayResponse = $this->runCurl($args, self::URL_PEDIDOS);

        print_r($arrayResponse);
    }

    /**
     * Genera un Token para el pedido
     * @param string $typeOfToken Tipo de token generado
     * @return string Token generado
     */
    private function generateToken($typeOfToken){
        return sha1(self::TOKEN_PRIVADO.$typeOfToken);
    }

    /**
     * Retorna las ciudades en forma de array
     * @param string $typeOfToken Tipo de token generado
     * @return string Token generado
     */
    public function consultarCiudades(){
        $token = $this->generateToken(self::TOKEN_TIPO_CIUDAD);

        $url = self::URL_BASE.'ciudades/1.1/traer';
        $args = ['token'=>$token,'token_publico'=> self::TOKEN_PUBLICO];
        $arrayResponse = $this->runCurl($args, $url);

        return $arrayResponse;
    }


    public function consultarCategorias(){
        $token = $this->generateToken(self::TOKEN_TIPO_CATEGORIA);

        $url = self::URL_BASE.'categorias/1.1/traer';
        $args = ['token'=>$token,'token_publico'=> self::TOKEN_PUBLICO];
        $arrayResponse = $this->runCurl($args, $url);
        return $arrayResponse;
    }

    #CALCULAR-FLETE
    public function calcularFlete($json){
        $token = $this->generateToken(self::TOKEN_TIPO_FLETE);

        $url = self::URL_BASE.'calcular-flete/1.1/traer';
        $args = ['token'=>$token,'token_publico'=> self::TOKEN_PUBLICO, 'dato'=> $json];
        $arrayResponse = $this->runCurl($args, $url);
        return $arrayResponse;
    }

    #registrar usuario
    public function registrarUsuario(array $json){
        $url = self::URL_BASE.'usuario/1.1/registro';
        $args = $json;
        $arrayResponse = $this->runCurl($args, $url);
        return $arrayResponse;
    }

}