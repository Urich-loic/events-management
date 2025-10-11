<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationShips;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationShips;
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }


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
        $relations = ['user', 'attendees', 'attendees.user'];
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
            'user_id' => $request->user()->id,
        ]);

        return $this->LoadRelationShips($event, $relations);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $relations = ['user', 'attendees', 'attendees.user'];

        $event->load('user', 'attendees');
        return new EventResource($this->LoadRelationShips($event, $relations));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'You are not authorized to update this event.');
        // }

        $this->authorize('update-event', $event);
        $relations = ['user', 'attendees', 'attendees.user'];

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'date',
            'end_time' => 'date|after_or_equal:start_time',
        ]);

        $event->update($validated);

        return new EventResource($this->LoadRelationShips($event, $relations));
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
