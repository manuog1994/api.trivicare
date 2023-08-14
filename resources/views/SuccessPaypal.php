<html> 
<body> 

<!-- Visualizar mensaje de espera con spinner -->
<div class="spinner-border text-primary" role="status">
  <span class="sr-only">Estamos procesando su pedido, espere...</span>
</div>

<?php

use App\Models\Order;
use App\Models\Reserve;



if (!empty( $_POST ) ) {//URL DE RESP. ONLINE

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
	if (!empty( $_GET ) ) {//URL DE RESP. ONLINE
		
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
	} 
}

		

?>
</body> 
</html> 