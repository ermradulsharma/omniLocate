<?php

namespace Skywalker\Location\Commands;

use Skywalker\Support\Console\Command;
use Skywalker\Location\Facades\Location;

class RefreshIpCache extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'location:refresh-cache {ip? : The IP address to refresh, or all if empty}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cached location data for IPs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $ip = $this->argument('ip');
        $ip = is_array($ip) ? ($ip[0] ?? null) : $ip;
        $ip = $ip ? (string) $ip : null;

        if ($ip) {
            $this->info("Refreshing cache for IP: $ip");
            // Force refresh logic (needs support in Location class to bypass cache or forget it)
            // For now, we assume we can forget it from cache using the standard cache key
            $key = "location.$ip";
            cache()->forget($key);
            Location::get($ip); // This will re-cache it
        } else {
            // Bulk refresh would require knowing all cached keys, which is hard with standard cache drivers.
            // This might just be a placebo without a list of IPs.
            // Alternatively, iterate over GeoAnalytics table if available to refresh recent IPs.
            $this->info('Refreshing cache for recent IPs from analytics...');

            if (class_exists(\Skywalker\Location\Models\GeoAnalytics::class)) {
                \Skywalker\Location\Models\GeoAnalytics::latest()->limit(100)->pluck('ip')->unique()->each(function (mixed $ip) {
                    $ipString = is_string($ip) || is_numeric($ip) ? (string) $ip : '';
                    if ($ipString === '') {
                        return;
                    }
                    $key = "location.$ipString";
                    cache()->forget($key);
                    Location::get($ipString);
                    $this->output->write('.');
                });
            }
        }

        $this->info('Done!');

        return static::SUCCESS;
    }
}
