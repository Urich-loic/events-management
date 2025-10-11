<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

trait CanLoadAttendees
{
    public function loadAttendeesRelation(
        Model|EloquentBuilder|QueryBuilder $for,
        ?array $relations = null
    ) {
        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeAttendees($relation),
                fn($q) => $for instanceof Model ? $for->load('attendees.user') : $q->with('attendees.user')
            );
        }
    }

    protected function shouldIncludeAttendees($relation)
    {
        $includes = request()->query('include', '');
        if (!$includes) {
            return false;
        }

        $relations = array_map('trim', explode(',', $includes));


        return in_array($relation, $relations);
    }
    //
}
