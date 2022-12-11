<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Refresh" content="10;url=http://localhost:3000/my-orders">
    <title>TriviCare | Detalle de su pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid bg-light" style="height: 100vh;">
        <div class="card w-75 m-auto p-5">
            <div class="card-body">
                <div class="d-flex justify-content-center mt-3 mb-5">
                    <img src="{{ asset('img/success.png') }}" alt="success" width="20%">
                </div>
                <div>
                    <h3>Gracias por su compra, su pago ha sido procesado con éxito.</h3>
                    <p>Número de pedido: {{ $order->id }}</p>
                    <p>Total del pedido: {{ round(($order->total * 1.21) + $order->shipping, 2) }} &euro;</p>
                    <p>Fecha del pedido: {{ $order->order_date }}</p>
                    <span>A continuación recibiras un correo electrónico con la factura y los detalles de tu pedido. </span>
                </div>

                <div class="d-flex justify-center mt-3">
                    <p class="font-italic">La página se rediccionará hacia sus pedidos en 5 segundos, si no es así pulse el botón "Ir a mis pedidos"</p>
                </div>

                <div class="d-flex justify-content-center mt-5">
                    <a href="http://localhost:3000/my-orders" class="btn btn-primary">Ir a mis pedidos</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>