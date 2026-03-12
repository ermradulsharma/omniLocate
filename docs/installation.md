# Installation & Configuration

## 📦 Installation

Requirements:

- PHP >= 8.2
- Laravel >= 10.0 (Supports 10-12+)
- [Skywalker Toolkit](https://github.com/skywalker-labs/toolkit) (Installed automatically)

Install via Composer:

```bash
composer require skywalker-labs/location
```

## ⚙️ Configuration

### 1. Publish Assets

Publish the configuration file, assets, and database migrations:

```bash
php artisan vendor:publish --provider="Skywalker\Location\LocationServiceProvider"
```

### 2. Database Migrations

OmniLocate uses a database table (`location_geo_analytics`) to store traffic logs and intelligent analytics. *Note: Table names are prefixed by default via the Toolkit.*

```bash
php artisan migrate
```

### 3. Setup Drivers

Open `config/location.php`. Set your preferred drivers. By default, OmniLocate uses `HttpHeader` and falls back to `IpApi`.

**Supported Drivers:**

- `MaxMind` (Local DB or Web Service)
- `IpApi` (Pro & Free)
- `IpInfo`
- `IpData`
- `GeoPlugin`
- `HttpHeader` (Cloudflare/AWS/Varnish headers)

Example `MaxMind` Local setup:

1. Download `GeoLite2-City.mmdb`.
2. Place it in `database/maxmind/`.
3. Update config path:

```php
'maxmind' => [
    'local' => ['path' => database_path('maxmind/GeoLite2-City.mmdb')]
]
```
