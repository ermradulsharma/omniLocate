<div align="center">

# 📍 OmniLocate

### **Elite Location Intelligence, Threat Monitoring & Hybrid Verification for Laravel**

[![Latest Version](https://img.shields.io/badge/version-3.0.0-blueviolet.svg?style=for-the-badge)](https://packagist.org/packages/skywalker-labs/location)
[![Laravel Version](https://img.shields.io/badge/Laravel-10--12-red.svg?style=for-the-badge)](https://laravel.com)
[![PHP Quality](https://img.shields.io/badge/PHPStan-Level%209-success.svg?style=for-the-badge)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge)](LICENSE.md)

**OmniLocate** is a high-performance geographical intelligence package built on the **Skywalker Toolkit Foundation**. It combines traditional IP geolocation with modern security features like **Hybrid GPS Verification**, **Bot Guard**, and **Adaptive Risk Scoring**.

[**Get Started**](#-quick-start) • [**Features**](#-pro-intelligence) • [**Security**](#-security--integrity) • [**Dashboard**](#-intelligence-dashboard)

</div>

---

## 💎 Why Choose OmniLocate?

OmniLocate isn't just a wrapper around IP-API. It is an **Elite-Grade** solution designed for applications where accuracy and security are non-negotiable.

*   **Extreme Type Safety**: 100% compliant with **PHPStan Level 9**.
*   **Hybrid Verification**: Detects sophisticated IP/VPN spoofing by cross-checking against device GPS.
*   **Action-Oriented Architecture**: Clean, scalable, and modular design inspired by elite industry standards.
*   **Zero-Config Security**: Built-in protection against SSRF, anonymous Tor traffic, and fake crawlers.

---

## 🚀 Quick Start

Perfect for getting your project up and running in minutes.

### 1. Installation

```bash
composer require skywalker-labs/location
```

### 2. Setup

```bash
php artisan vendor:publish --provider="Skywalker\Location\LocationServiceProvider"
php artisan migrate
```

### 3. Usage

```php
use Skywalker\Location\Facades\Location;

// Auto-detect visitor location
$position = Location::get(); 

echo $position->countryName; // e.g., "United States"
echo $position->cityName;    // e.g., "New York"
```

---

## 🧠 Pro Intelligence

### **A. In-View Location (Blade)**

Display location data to users without writing a single line of PHP:

```blade
Welcome from {{ @location('cityName') }}!
```

### **B. Hybrid Geolocation (The "Omni" Factor)**

Detect spoofing by comparing IP data with actual GPS coordinates:

```php
if ($position->is_spoofed) {
    // Distance > 500km between IP and GPS detected
    abort(403, 'Location Verification Failed');
}
```

### **C. Adaptive Rate Limiting**

Automatically slow down high-risk IPs while keeping normal users fast ⚡:

```php
Route::middleware(['location.rate-limit'])->group(function () {
    Route::post('/login', [LoginController::class, 'store']);
});
```

---

## 📊 Intelligence Dashboard

OmniLocate includes a comprehensive **real-time dashboard** to visualize your traffic, monitor threats, and track geographical trends.

*   **Real-time Traffic Mapping**
*   **Automated Bot Discovery**
*   **Threat & Block Analytics**

Access it at: `/omni-locate/dashboard`

---

## 🛡️ Security & Integrity

*   **Verified Crawler Logic**: Uses Reverse DNS to ensure Googlebot/Bingbot are legitimate before allowing access.
*   **SSRF Protection**: Hardened driver layer that blocks internal IP lookups and prevents data leakage.
*   **Trusted Proxy Support**: Fully compatible with Cloudflare, Akamai, and AWS load balancers.

---

## 🔧 Driver Support

Switch between 8+ enterprise drivers with intelligent failover:

*   **MaxMind** (Local & Web)
*   **IpInfo**, **IpData**, **IpApi**
*   **GeoPlugin**
*   **HttpHeader** (Proximity Headers)

---

## 🤝 Community & Support

*   **Contributing**: Read our [Contributing Guide](CONTRIBUTING.md).
*   **Security**: Report issues via [Security Policy](SECURITY.md).

Created & Maintained by **Skywalker-Labs Team**.
Distributed under the MIT License.
