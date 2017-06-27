<?php

/**
 * Archivo SDK de las funciones de consulta de Pagopar
 * @author "Pagopar" <desarrollo@pagopar.com>
 * @version 1 27/4/2017
 */

class ConsultPagopar{
    //URLs de configuración
    const URL_BASE = 'https://api.pagopar.com/api/';
    const URL_CATEGORIAS = self::URL_BASE.'categorias/1.1/traer';
    const URL_CIUDADES = self::URL_BASE.'ciudades/1.1/traer';

    //Tipos de Tokens generados
    const TOKEN_TIPO_CIUDAD = 'CIUDADES';
    const TOKEN_TIPO_CATEGORIA = 'CATEGORIAS';

    public $privateKey = null;
    public $publicKey = null;

    /**
     * Constructor de la clase
     */
    public function __construct() {

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
     * Genera un Token para el pedido
     * @param string $typeOfToken Tipo de token generado
     * @return string Token generado
     */
    private function generateToken($typeOfToken){
        return sha1($this->privateKey.$typeOfToken);
    }

    /**
     * Obtiene las ciudades de los productos
     * @return array $resultado Array de objetos con los atributos de las ciudades o,
     * en caso de error, un Array con resultado "Sin datos"
     */
    public function getCities(){
        $token = $this->generateToken(self::TOKEN_TIPO_CIUDAD);

        $args = ['token'=>$token,'token_publico'=>$this->publicKey];
        $response = $this->runCurl($args, self::URL_CIUDADES);

        return json_decode($response);
    }

    /**
     * Obtiene las categorías de los productos
     * @return array $resultado Array de objetos con los atributos de las categorías o,
     * en caso de error, un Array con resultado "Sin datos"
     */
    public function getProductCategories(){
        $token = $this->generateToken(self::TOKEN_TIPO_CATEGORIA);

        $args = ['token'=>$token,'token_publico'=>$this->publicKey];
        $response = $this->runCurl($args, self::URL_CATEGORIAS);
        $arrayResponse = json_decode($response);

        return $arrayResponse->resultado;
    }

}