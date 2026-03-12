<?php

namespace Skywalker\Location\Rules;

use Illuminate\Contracts\Validation\Rule;
use Skywalker\Location\Facades\Location;

class LocationRule implements Rule
{
    /**
     * The country name or code to validate against.
     *
     * @var string
     */
    protected $country;

    /**
     * Constructor.
     *
     * @param string $country
     */
    public function __construct($country)
    {
        $this->country = $country;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $position = Location::get(is_string($value) ? $value : null);

        if ($position instanceof \Skywalker\Location\DataTransferObjects\Position) {
            return strtolower((string) $position->countryCode) === strtolower((string) $this->country)
                || strtolower((string) $position->countryName) === strtolower((string) $this->country);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be located in ' . $this->country . '.';
    }
}
