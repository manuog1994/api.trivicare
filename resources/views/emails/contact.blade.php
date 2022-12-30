<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formulario de Contacto</title>
</head>
<body>
    Nombre: {{ $mailData['name'] }} 
    <br>
    Email: {{ $mailData['email'] }}
    <br>
    Asunto: {{ $mailData['subject'] }}
    <br>
    Mensaje: {{ $mailData['message'] }}
</body>
</html>