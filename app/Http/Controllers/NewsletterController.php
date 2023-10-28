<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cupon;
use App\Models\Newsletter;
use App\Mail\SubscribeMail;
use Illuminate\Support\Str;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use App\Mail\UnsubscribeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:newsletters',
            'phone' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dirección de correo ya está suscrita a nuestro boletín.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $newsletter = Newsletter::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        //Generador de cupón único
        $code = Str::random(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    
        while (Cupon::where('code', $code)->exists()) {
            $code = Str::random(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }

        // Si la fecha actual es menor que la fecha de expiración, no se genera cupón
        if (Carbon::now()->lessThan(Carbon::parse('2023-11-01'))) {
            $mailData = [
                'email' => $newsletter->email,
            ];

            Mail::to($newsletter->email)->send(new SubscribeMail($mailData));

        } else {
            //Guardar en tabla Cupones
            $coupon = Cupon::create([
                'code' => $code,
                'discount' => 15,
                'validity' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'status' => 2,
                'unique' => true,
            ]);

            $mailData = [
                'email' => $newsletter->email,
                'code' => $code,
            ];

            Mail::to($newsletter->email)->send(new SubscribeMail($mailData));
            
        }
        
        return response()->json(['message' => 'You are already subscribed to our newsletter.']); 

    
    }

    public function unsubscribe(Request $request)
    {
        $newsletter = Newsletter::where('email', $request->email)->first();

        $mailData = [
            'email' => $newsletter->email,
        ];

        Mail::to($newsletter->email)->send(new UnsubscribeMail($mailData));
        
        $newsletter->delete();

        return response()->json(['message' => 'You are already unsubscribed from our newsletter.']);
    }

    public function sendNewsletter(Request $request)
    {
        if($request->token == 'e5QVTEe-@j$beR5W7=r_zAt3') {
            $newsletters = Newsletter::all();

            foreach($newsletters as $newsletter) {
                $mailData = [
                    'email' => $newsletter->email,
                ];

                // enviar el correo cada 10 segundos
                sleep(10);
                Mail::to($newsletter->email)->send(new NewsletterMail($mailData));
            }

            return response()->json(['message' => 'Newsletter sent.']);
        } else {
            return response()->json(['message' => 'Unauthorized.']);
        }
    }
}
