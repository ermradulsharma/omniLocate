<?php

namespace Skywalker\Location\Models;

use Skywalker\Support\Database\PrefixedModel;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|GeoAnalytics query()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoAnalytics select(mixed ...$columns)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoAnalytics latest()
 * @method static \Illuminate\Database\Eloquent\Builder|GeoAnalytics where(string $column, mixed $operator = null, mixed $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|GeoAnalytics whereBetween(string $column, array $values)
 * @method static int count()
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class GeoAnalytics extends PrefixedModel
{
    protected $table = 'location_geo_analytics';

    /**
     * Get the parent authenticatable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function authenticatable()
    {
        return $this->morphTo();
    }

    protected $fillable = [
        'ip',
        'country_code',
        'city',
        'isp',
        'is_proxy',
        'is_vpn',
        'is_tor',
        'risk_score',
        'url',
        'method',
    ];
}
