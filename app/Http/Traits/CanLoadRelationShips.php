<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

trait CanLoadRelationShips
{
    public function LoadRelationShips(
        Model|EloquentBuilder|QueryBuilder $for,
        ?array $relations = null
    ) {

        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation) {
            $for->when(
                $this->shouldBeIncluded($relation),
                fn($q) => $for instanceof Model
                    ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;
    }


    protected function shouldBeIncluded($relation)
    {
        $includes = request()->query('include', '');
        if (!$includes) {
            return false;
        }

        $relations = array_map('trim', explode(',', $includes));

        return in_array($relation, $relations);
    }
}
