<?php

declare(strict_types=1);

namespace Skywalker\Location\Actions;

use Skywalker\Location\Facades\Location;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Support\Foundation\Action;

class MatchGeoRule extends Action
{
    /**
     * Match a rule against the current or provided position.
     *
     * DSL Format: "country:US,CA;risk<50;is_vpn:false"
     * Delimiters: ";" for AND, "," for OR (within value)
     * Operators: ":", "<", ">", "=", "!="
     *
     * @param  mixed  ...$args
     * @return bool
     */
    public function execute(...$args): bool
    {
        $rule = isset($args[0]) && is_string($args[0]) ? $args[0] : '';
        /** @var Position|null $position */
        $position = $args[1] ?? Location::get();

        if (! $position instanceof Position) {
            return false;
        }

        $conditions = explode(';', $rule);

        foreach ($conditions as $condition) {
            if (! $condition) {
                continue;
            }

            if (! $this->checkCondition($condition, $position)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check an individual condition.
     */
    protected function checkCondition(string $condition, Position $position): bool
    {
        if (str_contains($condition, '<')) {
            [$key, $value] = explode('<', $condition);
            $check = $this->getValue($key, $position);

            return (is_numeric($check) ? (int) $check : 0) < (int) $value;
        }

        if (str_contains($condition, '>')) {
            [$key, $value] = explode('>', $condition);
            $check = $this->getValue($key, $position);

            return (is_numeric($check) ? (int) $check : 0) > (int) $value;
        }

        if (str_contains($condition, '!=')) {
            [$key, $values] = explode('!=', $condition);
            $values = explode(',', $values);
            $check = $this->getValue($key, $position);

            return ! in_array(is_string($check) || is_numeric($check) ? (string) $check : '', $values, true);
        }

        if (str_contains($condition, ':')) {
            [$key, $values] = explode(':', $condition);
            $values = explode(',', $values);

            $check = $this->getValue($key, $position);

            if (isset($values[0]) && in_array(strtolower((string) $values[0]), ['true', 'false'], true)) {
                $expected = filter_var($values[0], FILTER_VALIDATE_BOOLEAN);

                return $check === $expected;
            }

            return in_array(is_string($check) || is_numeric($check) ? (string) $check : '', $values, true);
        }

        return false;
    }

    /**
     * Get the value for a given key from the position.
     *
     * @return mixed
     */
    protected function getValue(string $key, Position $position)
    {
        return match (trim($key)) {
            'country' => $position->countryCode,
            'risk' => $position->geoRiskScore,
            'is_vpn' => $position->isVpn,
            'is_proxy' => $position->isProxy,
            'is_tor' => $position->isTor,
            default => null,
        };
    }
}
