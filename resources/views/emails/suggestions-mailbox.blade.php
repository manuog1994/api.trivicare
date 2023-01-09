<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sugerencias</title>
</head>
<body>
    <h2>Nombre: {{ $mailData['name'] }}</h2>
    <p>Correo: {{ $mailData['email'] }}</p>
    <p>Tipo: {{ $mailData['type'] }}</p>
    <p>Mensaje: {{ $mailData['message'] }}</p>
</body>
</html>