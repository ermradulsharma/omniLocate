<p align="center">
    <img src="art/omnilocate_banner.png" alt="OmniLocate Banner" width="100%">
</p>

# OmniLocate

<p align="center">
    <a href="https://github.com/ermradulsharma/omniLocate/actions/workflows/run-tests.yml">
        <img src="https://img.shields.io/github/actions/workflow/status/ermradulsharma/omniLocate/run-tests.yml?branch=main&style=flat-square" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/ermradulsharma/omnilocate">
        <img src="https://img.shields.io/packagist/dt/ermradulsharma/omnilocate.svg?style=flat-square" alt="Total Downloads">
    </a>
    <a href="https://github.com/ermradulsharma/omniLocate/blob/main/LICENSE">
        <img src="https://img.shields.io/packagist/l/ermradulsharma/omnilocate.svg?style=flat-square" alt="License">
    </a>
</p>

**OmniLocate** is a premium, high-performance user location detection package for Laravel. It provides a robust, developer-friendly API for identifying visitor details via IP address, featuring advanced caching, multi-driver support with intelligent fallbacks, and seamless integration with CDNs like Cloudflare and Akamai.

---

## 🚀 Key Features

- **🌐 Intelligent Detection**: Works out-of-the-box with various IP services and CDN headers.
- **🛡️ Smart Fallbacks**: Chain multiple drivers to ensure location data is always available.
- **⚡ Performance First**: Integrated Laravel caching to minimize external API calls.
- **📏 Geo-Utilities**: Built-in distance calculation between any two detected points.
- **🤖 Bot Filtering**: Automatically skips detection for major search engine bots.
- **🧩 Fluent API**: Elegant, human-readable syntax for all operations.

---

## 🛠️ Installation

Install OmniLocate via Composer:

```bash
composer require ermradulsharma/omnilocate
```

The package will automatically register its service provider and facade.

### Configuration

Publish the configuration file to customize your drivers and settings:

```bash
php artisan vendor:publish --provider="Ermradulsharma\OmniLocate\LocationServiceProvider"
```

This creates `config/location.php`.

---

## 📖 Basic Usage

### Detect Current Visitor

Detect the location of the current user based on their IP address:

```php
use Ermradulsharma\OmniLocate\Facades\Location;

if ($position = Location::get()) {
    echo $position->countryName; // "India"
    echo $position->cityName;    // "Mumbai"
    echo $position->flag();      // "🇮🇳"
}
```

### Detect Specific IP

```php
$position = Location::get('8.8.8.8');
```

---

## 🔗 Advanced Driver Chain & Fallbacks

OmniLocate excels at reliability. You can define a default driver and any number of fallbacks in your configuration, or add them dynamically:

### Dynamic Fallbacks

```php
use Ermradulsharma\OmniLocate\Facades\Location;
use Ermradulsharma\OmniLocate\Drivers\IpApi;

// Add a fallback driver dynamically at runtime
Location::fallback(new IpApi());
```

---

## 🧩 Extra Features

### Blade Directives

Quickly display location info in your views:

```blade
Current Country: @location('countryName')
Your Flag: @location('flag')
```

### Distance Utilities

```php
$distance = $position->distanceTo($otherPosition); // Distance in KM
```

### Validation Rules

Ensure users are from a specific location:

```php
$request->validate([
    'signup_ip' => 'required|location:India',
]);
```

---

## 📦 Supported Drivers

- **HttpHeader** (CDN Headers - Default)
- **IpApi** (Free/Pro)
- **IpData**
- **IpInfo**
- **GeoPlugin**
- **MaxMind** (Local and Web Service)

---

## 🤝 Support & Funding

If you find this package useful, please consider supporting the developer:

- **Sponsor**: [Support Mradul Sharma on GitHub](https://github.com/sponsors/ermradulsharma)
- **Repo**: [GitHub Repository](https://github.com/ermradulsharma/omniLocate)

---

## ⚖️ License

Distributed under the MIT License. See `LICENSE` for more information.
