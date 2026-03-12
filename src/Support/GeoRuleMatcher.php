<?php

declare(strict_types=1);

namespace Skywalker\Location\Support;

use Skywalker\Location\Actions\MatchGeoRule;
use Skywalker\Location\DataTransferObjects\Position;

class GeoRuleMatcher
{
    /**
     * Match a rule against the current or provided position.
     *
     * @param  string  $rule
     * @param  Position|null  $position
     * @return bool
     */
    public static function matches(string $rule, ?Position $position = null): bool
    {
        return (bool) MatchGeoRule::run($rule, $position);
    }
}

