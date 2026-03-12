# Basic Usage

## 📍 Retrieving Location

### Current User

Get the location of the visitor making the current request. OmniLocate auto-resolves the IP and handles bot verification:

```php
use Skywalker\Location\Facades\Location;

$position = Location::get(); // Returns verified Position DTO
```

### Specific IP

Get location for any IP address:

```php
$position = Location::get('8.8.8.8');
```

## 📄 The Position DTO

The `Position` Data Transfer Object contains all available intelligence:

| Property       | Description            | Example                 |
| :------------- | :--------------------- | :---------------------- |
| `countryName`  | Full country name      | `"United States"`       |
| `countryCode`  | ISO 3166-1 Alpha-2     | `"US"`                  |
| `cityName`     | City                   | `"New York"`            |
| `latitude`     | GPS Latitude           | `40.7128`               |
| `longitude`    | GPS Longitude          | `-74.0060`              |
| `currencyCode` | Local Currency         | `"USD"`                 |
| `timezone`     | Timezone               | `"America/New_York"`    |
| `isProxy`      | Is using Proxy? (bool) | `false`                 |
| `isVpn`        | Is using VPN? (bool)   | `true`                  |
| `geoRiskScore` | Fraud Risk (0-100)     | `25`                    |

## 📐 Distance Calculations

Calculate the Haversine distance between two locations.

```php
$pos1 = Location::get('1.1.1.1');
$pos2 = Location::get('8.8.8.8');

// Get distance in Kilometers
$km = $pos1->distanceTo($pos2);

// Get distance in Miles
$miles = $pos1->distanceTo($pos2, 'miles');
```

## 🖥️ Blade Directives

Use directly in your Blade views:

```blade
@location('countryName')
<!-- Output: United States -->

@if(@location('isVpn'))
    <div class="badge badge-warning">VPN User Detected</div>
@endif
```

## 🛡️ Manual Verification

If you need to manually verify a bot or check a risk score:

```php
use Skywalker\Location\Actions\VerifyBot;

$isRealBot = app(VerifyBot::class)->execute($ip, $userAgent);
```
