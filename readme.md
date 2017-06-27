# SDK PHP Pagopar

## Primeros pasos

#### 1. Incluir la clase de Pagopar

Incluimos la clase de Pagopar que se encuentra en el archivo `Pagopar.php`.

```php
    require 'Pagopar.php';
```


#### 2. Usar una base de datos

**Es necesario crear una base de datos MySQL o usar una ya existente** antes de usar el SDK de Pagopar.

#### 3. Inicializar la base de datos

Instanciamos la clase `DBPagopar` con los datos necesarios para inicializar la base de datos.  El SDK automáticamente crea una tabla llamada `transactions` en la que se guardarán los pedidos.
 Los parámetros a ser pasados son todas cadenas: `dbname` (el nombre de la base de datos), `dbuser` (el usuario) y `dbpass` (la contraseña).


```php
    $db = new DBPagopar( 'dbname' , 'dbuser' , 'dbpass' );
```

#### 4. Elegir un nuevo id para un pedido

Debemos elegir un id para realizar un nuevo pedido o consultar una transacción realizada anteriormente. **En ambos casos el id debe ser un entero mayor a cero**.

En el caso de una **nueva transacción**, debemos elegir un **valor superior al id del último pedido realizado**.

## Ejemplo de nueva transacción

#### Instanciar el pedido

Lo primero que hacemos es crear una variable, en este caso `pedidoPagopar`, e instanciamos la clase de Pagopar, pasandole como parámetro el id del nuevo pedido.


```php
    $pedidoPagopar= new Pagopar( idNuevoPedido, $db );
```

#### Crear transacción de prueba

Para realizar una transacción de prueba podemos usar el método `newTestPagoparTransaction` que realiza un pedido de prueba con valores precargados.

```php
    $pedidoPagopar= new newTestPagoparTransaction( idNuevoPedido, $db );
```

Así, para generar un nuevo pedido de prueba de manera sencilla tenemos que:

```php
    require 'Pagopar.php';

    $db = new  DBPagopar( ’dbname ’ , ’dbuser ’ , ’dbpass ’ );
    /* Generar  nuevo  pedido */
    //Id  mayor  al Id del  ultimo  pedido , solo  para  pruebas
    $idNuevoPedido = nuevo_id;
    // Generamos  el  pedido
    $pedidoPagoPar = new  Pagopar($idNuevoPedido ,$db);
    $pedidoPagoPar ->newTestPagoparTransaction ();
```


Para nuestro ejemplo nos basta usar esta función, pero si se desea hacer una transacción con valores customizables se puede usar el método `newPagoparTransaction`.


#### Nueva transacción con valores personalizables

El siguiente código posee valores similares a los del método `newTestPagoparTransaction`, pero con algunas cadenas que deben ser reemplazadas por valores reales.

```php

    require 'Pagopar.php';

    $db = new DBPagopar( 'dbname' , 'dbuser' , 'dbpass' );

    /*Generar nuevo pedido*/
    //Id mayor al Id del último pedido, solo para pruebas
    $idNuevoPedido = nuevo_id;
    //Generamos el pedido
    $pedidoPagoPar = new Pagopar($idNuevoPedido, $db);

    //Creamos el comprador
    $buyer = new BuyerPagopar();
    $buyer->name            = 'Juan Perez';
    $buyer->public_key      = 'public_key';
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
    $item1->category            = 3;
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
    $item2->category            = 3;
    $item2->sellerPhone         = '0985885487';
    $item2->sellerEmail         = 'correo_electrónico';
    $item2->sellerAddress       = 'lorep ipsum dolor';
    $item2->sellerAddressRef    = '';
    $item2->sellerAddressCoo    = '-28.75438,-57.1580038';

    //Agregamos los productos al pedido
    $pedidoPagoPar->order->addPagoparItem($item1);
    $pedidoPagoPar->order->addPagoparItem($item2);

    //Pasamos los parámetros para el pedido
    $pedidoPagoPar->order->publicKey = 'clave pública';
    $pedidoPagoPar->order->privateKey = 'clave privada';
    $pedidoPagoPar->order->typeOrder = 'VENTA-COMERCIO';
    $pedidoPagoPar->order->desc = 'Entrada Retiro';
    $pedidoPagoPar->order->periodOfDaysForPayment = 1;
    $pedidoPagoPar->order->periodOfHoursForPayment = 0;

    //Hacemos el pedido
    $pedidoPagoPar->newPagoparTransaction();


  ```

#### Consultar transacción

 Para consultar un pedido realizado previamente debemos conocer el id del mismo. Una vez que lo tenemos, basta con llamar a `getPagoparOrderStatus`, la cual nos retorna un JSON con las indicaciones de la transacción.

```php

    require 'Pagopar.php';

    $db = new DBPagopar('dbname','dbuser','dbpass');

    /*Consultar pedido*/
    $idPedido = id_pedido;
    $pedidoPagopar = new Pagopar($idPedido, $db);
    $pedidoPagopar->getPagoparOrderStatus($idPedido);

   ```