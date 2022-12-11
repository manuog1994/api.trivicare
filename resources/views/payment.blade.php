<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Refresh" content="600;url=http://localhost:8000/cancel">
    <title>TriviCare | Pasarela de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style lang="scss">
        .panel-title {
            display: inline;
            font-weight: bold;
        }

        .display-table {
            display: table;
        }

        .display-tr {
            display: table-row;
        }

        .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 61%;
        }

        .color-theme {
            background-color: #2AB5B2;
            height: 100vh;
        }

        .btn-form{
        background-color: #f7f7f7;
        border: 1px solid #ebebeb;
        color: #333;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 20px;
        text-transform: capitalize;
        border-radius: 0;
        margin: 0;
        display: inline-block;
        line-height: 1;
        cursor: pointer;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }
    .btn-form:hover{
        background-color: #2AB5B2;
    }

    .btn-theme{
    background-color: #2AB5B2;
    width: 100%;
    color: $white;
    font-weight: 500;
    margin-top: 10px;
    padding: 14px 30px 13px;
        &:hover {
            background-color: black;
        }
    }

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="d-md-flex col-md-6 color-theme align-items-center d-sm-none">
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('img/TriviCare_byn Positivo.png') }}" alt="logo_trivicare" width="80%">
                </div>
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('img/stripe.png') }}" alt="logo_trivicare" width="80%">
                </div>
            </div>
            <div class="col-md-6 bg-light d-flex align-items-center">
                <div class="panel panel-default credit-card-box w-75 m-auto">
                    <div class="d-md-none d-sm-flex justify-content-center mt-5 mb-2">
                        <img src="{{ asset('img/TriviCare_byn Positivo.png') }}" alt="logo_trivicare" width="80%">
                    </div>
                    <div class="panel-heading display-table">
                        <div class="row display-tr">
                            <h3 class="panel-title display-td">Detalles del pago</h3>
                            <div class="display-td">
                                <img class="img-responsive pull-right" src="{{ asset('img/tarjetas.png') }}" width="50%">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="{{ route('stripe.post', [$order]) }}" method="post" class="require-validation"
                            data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                            id="payment-form">
                            @csrf
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required mb-2'>
                                    <label class='control-label'>Nombre de la Tarjeta</label> 
                                    <input class='form-control' size='4' type='text'>
                                </div>
                            </div>
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required mb-2'>
                                    <label class='control-label'>Número de la Tarjeta</label> <input autocomplete='off'
                                        class='form-control card-number' size='20' type='text'>
                                </div>
                            </div>
                            <div class='form-row row mb-2'>
                                <div class='col-xs-12 col-md-4 form-group cvc required'>
                                    <label class='control-label'>CVC</label> <input autocomplete='off'
                                        class='form-control card-cvc' placeholder='ex. 311' size='4' type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required mb-2'>
                                    <label class='control-label'>Mes de Expiración</label> <input
                                        class='form-control card-expiry-month' placeholder='MM' size='2' type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required mb-2'>
                                    <label class='control-label'>Año de Expiración</label> <input
                                        class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text'>
                                </div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="true" id="check">
                                <label class="form-check-label" for="flexCheckDefault">
                                  He leído y acepto los <a href="#">términos y condiciones</a>
                                </label>
                            </div>
                            <div class="d-flex justify-content-end mb-2">
                                <h4>Total a pagar: <span>{{ round(($order->total * 1.21) + $order->shipping, 2) }} &euro;</span></h4>
                            </div>
                            <div class='form-row row'>
                                <div class='col-md-12 error form-group d-none'>
                                    <div class='alert-danger alert'>Please correct the errors and try
                                        again.</div>
                                </div>
                            </div>

                            <div class="row mt-2 mb-5 d-flex justify-content-between">
                                <div class="col-6">
                                    <button id="button-pay" class="btn btn-theme disabled" type="submit">Pagar</button>
                                </div>
                                <div class="col-6 mt-2" style="text-align:right;">
                                    <a class="text-dark mt-2" href="http://localhost:8000/cancel">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    let checked = document.getElementById("check").addEventListener("change", function () {
        if (this.checked) {
            document.getElementById("button-pay").classList.remove("disabled");
        } else {
            document.getElementById("button-pay").classList.add("disabled");
        }
    });
    $(function () {
        var $form = $(".require-validation");
        $('form.require-validation').bind('submit', function (e) {
            var $form = $(".require-validation"),
                inputSelector = ['input[type=email]', 'input[type=password]',
                    'input[type=text]', 'input[type=file]',
                    'textarea'
                ].join(', '),
                $inputs = $form.find('.required').find(inputSelector),
                $errorMessage = $form.find('div.error'),
                valid = true;
            $errorMessage.addClass('hide');
            $('.has-error').removeClass('has-error');
            $inputs.each(function (i, el) {
                var $input = $(el);
                if ($input.val() === '') {
                    $input.parent().addClass('has-error');
                    $errorMessage.removeClass('hide');
                    e.preventDefault();
                }
            });
            if (!$form.data('cc-on-file')) {
                e.preventDefault();
                Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                Stripe.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
            }
        });

        function stripeResponseHandler(status, response) {
            let botton = document.getElementById('button-pay').classList.add('disabled');
            if (response.error) {
                $('.error')
                    .removeClass('d-none')
                    .find('.alert')
                    .text(response.error.message);
                    $('#button-pay').removeClass('disabled');

            } else {
                /* token contains id, last4, and card type */
                var token = response['id'];
                var total = {{ $order->total }};
                var shipping = {{ $order->shipping }};
                var amount = ((total * 1.21) + shipping).toFixed(2);
                var orderId = {{ $order->id }};
                $form.find('input[type=text]').empty();
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.find('input[type=number]').empty();
                $form.append("<input type='hidden' name='amount' value='" + amount + "'/>");
                $form.find('input[type=number]').empty();
                $form.append("<input type='hidden' name='orderId' value='" + orderId + "'/>");
                $form.get(0).submit();
            }
        }
    });

</script>

</html>
