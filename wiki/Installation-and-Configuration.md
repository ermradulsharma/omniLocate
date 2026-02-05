# Installation & Configuration

## Installation

Install OmniLocate via Composer:

```bash
composer require ermradulsharma/omnilocate
```

The package will automatically register its service provider and facade.

## Configuration

Publish the configuration file to customize your drivers and settings:

```bash
php artisan vendor:publish --provider="Ermradulsharma\OmniLocate\LocationServiceProvider"
```

This creates `config/location.php` (or `config/config.php` depending on your setup).

### Configuration Options

The configuration file allows you to set up drivers, fallbacks, caching, and more.

#### Default Driver

Set the default driver used for location retrieval.

```php
'driver' => Ermradulsharma\OmniLocate\Drivers\HttpHeader::class,
```

#### Fallbacks

Define a list of drivers to use if the default driver fails.

```php
'fallbacks' => [
    Ermradulsharma\OmniLocate\Drivers\IpApi::class,
    Ermradulsharma\OmniLocate\Drivers\IpInfo::class,
    // ...
],
```

#### Caching

Enable caching to store location results and reduce API calls.

```php
'cache' => [
    'enabled' => env('LOCATION_CACHE', false),
    'duration' => 86400, // 24 hours
],
```

#### Bot Detection

Skip location detection for bots to save resources.

```php
'bots' => [
    'enabled' => true,
    'list' => [ 'googlebot', 'bingbot', ... ],
],
```

### API Keys

Some drivers require API keys. You should add these to your `.env` file and reference them in `config/location.php`.

```env
IP_API_TOKEN=your-token-here
IPINFO_TOKEN=your-token-here
IPDATA_TOKEN=your-token-here
```

Refer to the [Drivers](Drivers) page for detailed configuration for each driver.
