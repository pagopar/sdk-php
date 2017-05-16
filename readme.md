# SDK Pagopar

## Primeros pasos

#### 1. Usar una base de datos

**Es necesario crear una base de datos MySQL o usar una ya existente** antes de usar el SDK de Pagopar.

#### 2. Inicializar la base de datos

Instanciamos la clase `DBPagopar` con los datos necesarios para inicializar la base de datos. El SDK automáticamente crea una tabla llamada `transactions` en la que se guardarán los pedidos.
 Los parámetros a ser pasados son todas cadenas: `dbname` (el nombre de la base de datos), `dbuser` (el usuario) y `dbpass` (la contraseña).


```php
 $db = new DBPagopar( dbname , dbuser , dbpass );
```

#### 3. Elegir un nuevo id para un pedido

Debemos elegir un id para realizar un nuevo pedido o consultar una transacción realizada anteriormente. **En ambos casos el id debe ser un entero mayor a cero**.

En el caso de una **nueva transacción**, debemos elegir un **valor superior al id del último pedido realizado**.

## Realizar un nuevo pedido

## Código de ejemplo completo

```php

    $db = new DBPagopar( 'dbname' , 'dbuser' , 'dbpass' );

    /*Generar nuevo pedido*/
    //Id mayor al Id del último pedido, solo para pruebas
    $idNuevoPedido = nuevo_id;
    //Generamos el pedido
    $pedidoPagoPar = new Pagopar($idNuevoPedido, $db);

    //Creamos el comprador
    $buyer = new BuyerPagopar();
    $buyer->name            = 'Juan Perez';
    $buyer->email           = 'correo_electrónico';
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
    $item1->price               = 10000;
    $item1->cityId              = 1;
    $item1->desc                = "producto";
    $item1->url_img             = "https://www.hendyla.com/images/lazo_logo.png";
    $item1->weight              = '0.1';
    $item1->sellerPhone         = '0985885487';
    $item1->sellerEmail         = 'correo_electrónico';
    $item1->sellerAddress       = 'lorep ipsum';
    $item1->sellerAddressRef    = '';
    $item1->sellerAddressCoo    = '-28.75438,-57.1580038';

    $item2 = new ItemPagopar();
    $item2->name                = "Heladera";
    $item2->qty                 = 1;
    $item2->price               = 785000;
    $item2->cityId              = 1;
    $item2->desc                = "producto";
    $item2->url_img             = "https://www.hendyla.com/images/lazo_logo.png";
    $item2->weight              = '5.0';
    $item2->sellerPhone         = '0985885487';
    $item2->sellerEmail         = 'correo_electrónico';
    $item2->sellerAddress       = 'lorep ipsum dolor';
    $item2->sellerAddressRef    = '';
    $item2->sellerAddressCoo    = '-28.75438,-57.1580038';

    //Agregamos los productos al pedido
    $this->order->addPagoparItem($item1);
    $this->order->addPagoparItem($item2);

    //Pasamos los parámetros para el pedido
    $this->order->publicKey = 'clave pública';
    $this->order->privateKey = 'clave privada';
    $this->order->typeOrder = 'VENTA-COMERCIO';
    $this->order->desc = 'Entrada Retiro';
    $this->order->periodDays = 1;
    $this->order->periodDays = 0;

    //Hacemos el pedido
    $this->newPagoparTransaction();


  ```