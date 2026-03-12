# OmniLocate Features (v2.0 Elite)

## 🌍 Core Geo Intelligence (Toolkit Enabled)

- **Standardized Responses**: Every API endpoint uses `skywalker-labs/toolkit`'s `ApiResponse` for consistent JSON wrappers.
- **Accurate IP → Location Detection**: Multi-driver architecture (MaxMind, IpApi, Cloudflare, etc.) to pinpoint user location.
- **ISP & ASN Detection**: Identify Internet Service Providers and Autonomous System Numbers.
- **Currency & Timezone**: Auto-suggest local currency and timezone based on location.

## 🛡️ Network & Proxy Intelligence

- **VPN & Proxy Detection**: Real-time identification of anonymous proxies and commercial VPN services.
- **Tor Exit Nodes**: Built-in `TorBlocker` middleware specifically targeting anonymous threats.
- **Hosting/Datacenter**: Flag IPs belonging to hosting providers (AWS, DigitalOcean) often used by bots.

## 🚨 Risk Intelligence Engine

- **Unified Risk Score (0–100)**: A simplified risk score for every IP based on multiple security factors.
- **Adaptive Rate Limiting**: Automatically apply stricter throttling for high-risk IPs (configurable).

## 🧠 Elite Verification Layer

- **Hybrid Geolocation**: Verify IP location against Browser/Device GPS coordinates to detect spoofing.
- **Verified Bot Intelligence**: Reverse DNS verification to distinguish real Googlebots/Bingbots from fakes.
- **Internal IP Filtering**: Secure-by-default logic that prevents processing of private/reserved IP ranges.

## ⚡ Monitoring & Performance

- **Intelligence Dashboard**: Visual threat map and analytics dashboard to monitor traffic and risk trends.
- **High-Performance Caching**: Intelligent TTL-based local caching using Laravel's Cache facade.
- **Extreme Strictness**: Engineered for reliability with **PHPStan Level 9** type safety.

## 🔒 Privacy & Safety

- **Middleware Guards**: Plug-and-play `GeoRestriction`, `GeoRiskGuard`, and `TorBlocker`.
- **Anonymization**: Built-in support for GDPR/CCPA compliant IP anonymization.
