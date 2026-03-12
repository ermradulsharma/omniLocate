<?php

namespace Skywalker\Location\Exceptions;

final class DriverDoesNotExistException extends LocationException
{
    /**
     * Create a new exception for the non-existent driver.
     *
     * @param string $driver
     *
     * @return self
     */
    public static function forDriver($driver)
    {
        return new self(
            "The location driver [$driver] does not exist. Did you publish the configuration file?"
        );
    }
}

