<?php

namespace App\Http\Controllers\Api\EventNot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventNotResource;

class EventNotController extends Controller
{
    public function index()
    {
        $events = \App\Models\EventNot::all();
        return EventNotResource::collection($events);
    }

    public function store(Request $request)
    {
        $event = \App\Models\EventNot::create($request->all());
        return new EventNotResource($event);
    }

    public function show($id)
    {
        $event = \App\Models\EventNot::findOrFail($id);
        return new EventNotResource($event);
    }

    public function destroy($id)
    {
        $event = \App\Models\EventNot::findOrFail($id);
        $event->delete();
        return new EventNotResource($event);
    }

    public function destroyAll() {
        $events = \App\Models\EventNot::all();
        foreach ($events as $event) {
            $event->delete();
        }
        return EventNotResource::collection($events);
    }
}
