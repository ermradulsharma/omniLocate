<?php

namespace Ermradulsharma\OmniLocate\Traits;

use Ermradulsharma\OmniLocate\Facades\Location as LocationFacade;
use Ermradulsharma\OmniLocate\Models\Location as LocationModel;

trait UsesLocation
{
    /**
     * The location relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function location()
    {
        return $this->morphOne(LocationModel::class, 'authenticatable');
    }

    /**
     * Detect and save the model's location.
     *
     * @param  string|null  $ip
     * @return \Ermradulsharma\OmniLocate\Models\Location|null
     */
    public function detectLocation($ip = null)
    {
        if ($position = LocationFacade::get($ip)) {
            return $this->location()->updateOrCreate([], $position->toArray());
        }

        return null;
    }
}
