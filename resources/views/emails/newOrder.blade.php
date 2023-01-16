<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Has recibido un Pedido a través de la página</title>
</head>
<body>
    <h4>Tienes un nuevo pedido en la página</h4>
    <p>El pedido fue realizado por: {{ $or['name'] }}</p>
    <p>Su provincia de envío es {{ $or['state'] }}</p>
</body>
</html>