# Drivers

OmniLocate supports multiple drivers to ensure reliable location detection. You can configure the default driver and fallbacks in `config/location.php`.

## Supported Drivers

### HttpHeader (Default)

Detects the location based on headers provided by CDNs or load balancers (e.g., Cloudflare's `CF-IPCountry`).

**Configuration:**
This driver is usually the fastest as it doesn't require an external API call if your server acts as the origin behind a CDN.

### IpApi

Uses the free [ip-api.com](http://ip-api.com) service.

**Note:** The free version does not require an API key but has rate limits and does not support HTTPS.

### IpApiPro

Uses the pro version of [ip-api.com](http://members.ip-api.com).

**Configuration:**
Add your key to `.env`:

```env
IP_API_TOKEN=your-key
```

### IpInfo

Uses [ipinfo.io](https://ipinfo.io).

**Configuration:**
Add your token to `.env`:

```env
IPINFO_TOKEN=your-token
```

### IpData

Uses [ipdata.co](https://ipdata.co).

**Configuration:**
Add your token to `.env`:

```env
IPDATA_TOKEN=your-token
```

### GeoPlugin

Uses [geoplugin.com](http://www.geoplugin.com). Simple and effective.

### MaxMind

Supports both the GeoLite2 database (local) and the MaxMind Precision Web Services.

**Local Database:**

1. Download `GeoLite2-City.mmdb`.
2. Place it in `database/maxmind/GeoLite2-City.mmdb` (or configure the path in `config/location.php`).
3. Set `maxmind.web.enabled` to `false`.

**Web Service:**

1. Set `maxmind.web.enabled` to `true`.
2. Configure `user_id` and `license_key` in `config/location.php`.

## Fallbacks

You can chain drivers to ensure that if the primary driver fails (e.g., API downtime, rate limit reached), the next one takes over.

```php
// config/location.php
'fallbacks' => [
    Ermradulsharma\OmniLocate\Drivers\IpApi::class,
    Ermradulsharma\OmniLocate\Drivers\IpInfo::class,
],
```

## Runtime Fallbacks

You can also add fallbacks dynamically at runtime:

```php
use Ermradulsharma\OmniLocate\Facades\Location;
use Ermradulsharma\OmniLocate\Drivers\IpInfo;

Location::fallback(new IpInfo());
```

## Creating Custom Drivers

To create a custom driver, extend the `Ermradulsharma\OmniLocate\Drivers\Driver` abstract class.

```php
namespace App\Location\Drivers;

use Ermradulsharma\OmniLocate\Drivers\Driver;
use Ermradulsharma\OmniLocate\Position;
use Illuminate\Support\Fluent;

class MyCustomDriver extends Driver
{
    protected function url($ip)
    {
        return "https://api.example.com/locate/{$ip}";
    }

    protected function hydrate(Position $position, Fluent $location)
    {
        $position->countryName = $location->country_name;
        // Populate other fields...
        return $position;
    }

    protected function process($ip)
    {
        // Fetch data and return as Fluent object or false on failure
    }
}
```
