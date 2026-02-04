<?php

namespace Ermradulsharma\OmniLocate\Events;

use Ermradulsharma\OmniLocate\Position;

class LocationDetected
{
    /**
     * The detected position.
     *
     * @var Position
     */
    public $position;

    /**
     * Constructor.
     *
     * @param  Position  $position
     */
    public function __construct(Position $position)
    {
        $this->position = $position;
    }
}
