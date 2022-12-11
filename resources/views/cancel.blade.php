<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TriviCare | Pedido Cancelado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid bg-light" style="height: 100vh;">
        <div class="card w-75 m-auto p-5">
            <div class="card-body">
                <div class="d-flex justify-content-center mt-3 mb-5">
                    <img src="{{ asset('img/cancel.png') }}" alt="success" width="20%">
                </div>
                <div class="d-flex justify-content-center">
                    <h3>Su pedido ha sido cancelado</h3>
                </div>
                <div class="d-flex justify-content-center">
                    <p>Si aún desea realizar el pago, haga <a href="http://localhost:3000/my-orders">click aquí</a></p>
                </div>

                <div class="d-flex justify-content-center mt-5">
                    <a href="http://localhost:3000/shop" class="btn btn-primary">Volver a la tienda</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>