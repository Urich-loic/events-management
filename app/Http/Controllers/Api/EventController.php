<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationShips;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use CanLoadRelationShips;
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $relations = ['user', 'attendees', 'attendees.user'];
        $query = $this->LoadRelationShips(Event::query(), $relations);



        return EventResource::collection($query->latest()->paginate());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
        ]);

        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after_or_equal:start_time',
            ]),
            'user_id' => 1
        ]);

        return $this->LoadRelationShips($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($this->LoadRelationShips($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'date',
            'end_time' => 'date|after_or_equal:start_time',
        ]);

        $event->update($validated);

        return new EventResource($this->LoadRelationShips($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
