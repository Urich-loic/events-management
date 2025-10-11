<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadAttendees;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    use CanLoadAttendees;
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    public function index(Event $event)
    {
        $query = Attendee::query();
        $relations = ['attendees', 'attendees.user'];
        // $attendees = $event->attendees()->latest();

        foreach ($relations as $relation) {
            $query->when(
                $this->shouldInclude($relation),
                fn($q) => $q->with($relation)
            );
        }
        // $query = $this->loadAttendeesRelation($attendees, ['attendees,attendees.user']);
        return AttendeeResource::collection($query->latest()->paginate());
    }

    function shouldInclude($relation)
    {
        $includes = request()->query('include', '');
        if (!$includes) {
            return false;
        }

        $relations = array_map('trim', explode(',', $includes));


        return in_array($relation, $relations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => 1,
        ]);

        return new AttendeeResource($attendee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($attendee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $this->authorize('delete-attendee', [$event, $attendee]);
        $attendee->delete();
        return response(status: 204);
    }
}
