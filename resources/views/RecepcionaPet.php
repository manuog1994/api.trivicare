<html> 
<body> 

<!-- Visualizar mensaje de espera con spinner -->
<div class="spinner-border text-primary" role="status">
  <span class="sr-only">Estamos procesando su pedido, espere...</span>
</div>

<?php

use App\Models\Order;
use App\Models\Reserve;
use App\Libraries\Redsys\RedsysAPI;

	// Se crea Objeto
	$miObj = new RedsysAPI;


if (!empty( $_POST ) ) {//URL DE RESP. ONLINE
					
					$version = $_POST["Ds_SignatureVersion"];
					$datos = $_POST["Ds_MerchantParameters"];
					$signatureRecibida = $_POST["Ds_Signature"];
					

					$decodec = $miObj->decodeMerchantParameters($datos);	
					$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES
					$firma = $miObj->createMerchantSignatureNotif($kc,$datos);	

					echo PHP_VERSION."<br/>";
					echo $firma."<br/>";
					echo $signatureRecibida."<br/>";
					if ($firma === $signatureRecibida){
						//recuperar parametros de la url
						$order_id = $_GET['order_id'];

						//ejecutar el pago
						$order = Order::where('id', $order_id)->first();

						//cambiar estado del pedido
						$order->status = 1;
						$order->paid = 'PAGADO';
						$order->save();
						
						//redirigir a una url concreta
						sleep(1);
						header('Location:' . config('services.urlWeb.url') . '/success?order_id='.$order_id);
						exit();


					} else {
						echo "No se ha podido realizar el pedido. Por favor, contacte con nosotros. Gracias.";
					}
	}
	else{
		if (!empty( $_GET ) ) {//URL DE RESP. ONLINE
				
			$version = $_GET["Ds_SignatureVersion"];
			$datos = $_GET["Ds_MerchantParameters"];
			$signatureRecibida = $_GET["Ds_Signature"];
				
		
			$decodec = $miObj->decodeMerchantParameters($datos);
			$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES
			$firma = $miObj->createMerchantSignatureNotif($kc,$datos);
		
			if ($firma === $signatureRecibida){
				//recuperar parametro order_id de la url
				$order_id = $_GET['order_id'];

				//ejecutar el pago
				$order = Order::where('id', $order_id)->first();

				//cambiar estado del pedido
				$order->status = 1;
				$order->paid = 'PAGADO';
				$order->save();
				
				//redirigir a una url concreta
				sleep(1);
				header('Location:' . config('services.urlWeb.url') . '/success?order_id='.$order_id);
				exit();
			} else {
				echo "FIRMA KO";
			}
		}
		else{
			die("No se recibió respuesta");
		}
	}

?>
</body> 
</html> 