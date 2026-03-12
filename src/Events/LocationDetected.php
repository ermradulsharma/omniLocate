<?php

namespace Skywalker\Location\Events;

use Skywalker\Location\DataTransferObjects\Position;

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

