<?php

namespace Ermradulsharma\OmniLocate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ermradulsharma\OmniLocate\Facades\Location;

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
        if ($position = Location::get($value)) {
            return strtolower($position->countryCode) === strtolower($this->country)
                || strtolower($position->countryName) === strtolower($this->country);
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
