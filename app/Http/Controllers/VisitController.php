<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class VisitController extends Controller
{
    public function incrementVisit(Request $request)
    {
        $page = $request->input('page');
        $currentDate = Carbon::now()->toDateString(); // Obtiene la fecha actual

        // Busca una visita para la página y fecha actual o crea una nueva entrada
        $visit = Visit::firstOrNew(['page' => $page, 'date' => $currentDate]);
        $visit->count += 1;
        $visit->save();

        return response()->json($visit);
    }

    public function getVisits()
    {
        $visits = Visit::all();

        return response()->json($visits);
    }
}
