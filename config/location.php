<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | The default driver you would like to use for location retrieval.
    |
    */

    'driver' => Skywalker\Location\Drivers\HttpHeader::class,

    /*
    |--------------------------------------------------------------------------
    | Driver Fallbacks
    |--------------------------------------------------------------------------
    |
    | The drivers you want to use to retrieve the users location
    | if the above selected driver is unavailable.
    |
    | These will be called upon in order (first to last).
    |
    */

    'fallbacks' => [

        Skywalker\Location\Drivers\IpApi::class,

        Skywalker\Location\Drivers\IpInfo::class,

        Skywalker\Location\Drivers\GeoPlugin::class,

        Skywalker\Location\Drivers\MaxMind::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Position
    |--------------------------------------------------------------------------
    |
    | Here you may configure the position instance that is created
    | and returned from the above drivers. The instance you
    | create must extend the built-in Position class.
    |
    */

    'position' => Skywalker\Location\DataTransferObjects\Position::class,

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | If you want to cache the location results for a given IP address,
    | set 'enabled' to true. The duration is in seconds.
    |
    */

    'cache' => [

        'enabled' => env('LOCATION_CACHE', false),

        'duration' => 86400,

    ],

    /*
    |--------------------------------------------------------------------------
    | MaxMind Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the MaxMind driver.
    |
    | If web service is enabled, you must fill in your user ID and license key.
    |
    | If web service is disabled, it will try and retrieve the users location
    | from the MaxMind database file located in the local path below.
    |
    */

    'maxmind' => [

        'web' => [

            'enabled' => false,

            'user_id' => '',

            'license_key' => '',

            'options' => [

                'host' => 'geoip.maxmind.com',

            ],

        ],

        'local' => [

            'path' => database_path('maxmind/GeoLite2-City.mmdb')

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | IP API Pro Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the IP API Pro driver.
    |
    */

    'ip_api' => [

        'token' => env('IP_API_TOKEN'),

    ],

    /*
    |--------------------------------------------------------------------------
    | IPInfo Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the IPInfo driver.
    |
    */

    'ipinfo' => [

        'token' => env('IPINFO_TOKEN'),

    ],

    /*
    |--------------------------------------------------------------------------
    | IPData Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the IPData driver.
    |
    */

    'ipdata' => [

        'token' => env('IPDATA_TOKEN'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Localhost Testing
    |--------------------------------------------------------------------------
    |
    | If your running your website locally and want to test different
    | IP addresses to see location detection, set 'enabled' to true.
    |
    | The testing IP address is a Google host in the United-States.
    |
    */

    'testing' => [

        'enabled' => env('LOCATION_TESTING', true),

        'ip' => '66.102.0.0',

    ],

    /*
    |--------------------------------------------------------------------------
    | Smart Bot Detection (Active Security)
    |--------------------------------------------------------------------------
    |
    | When enabled, OmniLocate will perform advanced verification for search
    | engine bots. It checks the User-Agent AND performs a Reverse DNS lookup
    | to verify the IP truly belongs to Google, Bing, etc.
    |
    | 'enabled': Set to true to activate bot verification.
    | 'list': List of bot substrings to match in User-Agent.
    | 'trusted_domains': The required domain suffixes for Reverse DNS verification.
    |
    */
    'bots' => [

        'enabled' => true,

        'list' => [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'bruinbot',
            'facebot',
            'ia_archiver',
        ],

        'trusted_domains' => [
            'googlebot' => ['.googlebot.com', '.google.com'],
            'bingbot' => ['.search.msn.com'],
            'slurp' => ['.crawl.yahoo.net'],
            'duckduckbot' => ['.duckduckgo.com'],
            'yandexbot' => ['.yandex.com', '.yandex.ru', '.yandex.net'],
            'baiduspider' => ['.baidu.com', '.baidu.jp'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Geo Restriction & Firewall
    |--------------------------------------------------------------------------
    |
    | Control which countries can access your application.
    |
    | 'allowed_countries': If not empty, ONLY these ISO codes (e.g., 'US') are allowed.
    | 'blocked_countries': These ISO codes will be blocked (403 Forbidden).
    |
    */
    'restriction' => [
        'allowed_countries' => [], // e.g., ['US', 'CA', 'GB']
        'blocked_countries' => [], // e.g., ['RU', 'CN', 'NK']
    ],

    /*
    |--------------------------------------------------------------------------
    | Geo Risk Guard
    |--------------------------------------------------------------------------
    |
    | Automatically block high-risk IP addresses based on their MaxMind/IP-API score.
    |
    | 'threshold': The risk score (0-100) above which a request is blocked.
    |              0 = Safe, 100 = High Fraud Risk.
    |
    */
    'risk' => [
        'threshold' => 80, // Recommended: 75-85 for strict security
    ],

    /*
    |--------------------------------------------------------------------------
    | Tor Network Blocker
    |--------------------------------------------------------------------------
    |
    | Determine whether to block traffic originating from known Tor exit nodes.
    | This is useful for preventing anonymous abuse/spam.
    |
    */
    'tor' => [
        'block' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Hybrid Geolocation (The "Omni" Factor)
    |--------------------------------------------------------------------------
    |
    | Detects spoofing by comparing the User's IP Location vs their Physical
    | GPS Location (provided via frontend).
    |
    | 'threshold': The maximum allowed distance (in km) between IP and GPS.
    |              If the distance exceeds this, the user is flagged as spoofing.
    |
    */
    'hybrid' => [
        'threshold' => 500, // Distance > 500km considered 'Spoofed'
    ],

    /*
    |--------------------------------------------------------------------------
    | Visual Intelligence Dashboard
    |--------------------------------------------------------------------------
    |
    | OmniLocate includes a built-in dashboard to visualize traffic, threats,
    | and blocked requests in real-time.
    |
    | Access: /omni-locate/dashboard
    |
    | 'enabled': Set to false to disable the dashboard routes entirely.
    |
    */
    'dashboard' => [
        'enabled' => env('LOCATION_DASHBOARD_ENABLED', true),
        'middleware' => ['web'], // Add protection: ['web', 'auth']
    ],

];


