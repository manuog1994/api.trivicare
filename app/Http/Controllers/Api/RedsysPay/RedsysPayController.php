<?php

namespace App\Http\Controllers\Api\RedsysPay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Redsys\RedsysAPI;
use App\Models\Order;

class RedsysPayController extends Controller
{
    public function payment(Request $request)
    {
        // Hacer una petición POST a la URL de Redsys
        // https://sis-t.redsys.es:25443/sis/realizarPago
        // https://sis.redsys.es/sis/realizarPago (producción)

        $order = Order::where('id', $request->order_id)->first();
        $order->token_id = $request->token_id;
        $order->save();
        
        // Valores de entrada
        $fuc = config('services.getnet.merchant_code');
        $terminal = config('services.getnet.terminal');
        $moneda = "978";
        $trans = "0";
        $url = "";
        $urlOK = config('services.getnet.url_ok') . $order->token_id;
        $urlKO = config('services.getnet.url_ko');
        $id = time();
        $amount = strval($request->amount);
        $merchantCode256 = config('services.getnet.merchant_code_256');
        

        $miObj = new RedsysAPI;

        // Se Rellenan los campos
        $miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
        $miObj->setParameter("DS_MERCHANT_ORDER",$id);
        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
        $miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
        $miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
        $miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
        $miObj->setParameter("DS_MERCHANT_URLOK",$urlOK);
        $miObj->setParameter("DS_MERCHANT_URLKO",$urlKO);

        $version="HMAC_SHA256_V1";
        $kc = $merchantCode256;//Clave recuperada de CANALES
        // Se generan los parámetros de la petición
        $request = "";
        $params = $miObj->createMerchantParameters();
        $signature = $miObj->createMerchantSignature($kc);


        return response()->json([
            'parameters' => $params,
            'signature' => $signature,
            'version' => $version,
            'order' => $order,
        ], 200);
    }
}
