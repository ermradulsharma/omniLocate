# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2026-03-12

### Added

- **Elite Architecture**: Migrated to Action-oriented architecture using `Actions`, `Services`, and `DataTransferObjects`.
- **Skywalker Toolkit**: Integrated `skywalker-labs/toolkit` as the foundation for standardized API responses and models.
- **Extreme Strictness**: Achieved 100% type safety with **PHPStan Level 9** compliance.
- **Intelligence Dashboard**: Real-time traffic map and threat monitor with configurable middleware protection.
- **Hybrid Verification**: GPS vs IP distance-based spoofing detection.
- **Enhanced Bot Security**: Reverse DNS and Trusted Domain verification for legitimate crawlers.
- **Adaptive Rate Limiting**: Dynamic throttling based on IP risk scores.

### Changed

- **Directory Refactor**: Reorganized source code into logical layers (`src/Http`, `src/Actions`, `src/DataTransferObjects`, `src/Support`).
- **Standardized DTOs**: Migrated `Position` to a typed DTO for consistent data handling.
- **Hardened Drivers**: All drivers now filter internal/private IP ranges by default for SSRF protection.
- **Modern Naming**: Applied modern PHP naming conventions and strict typing across the package.

## [1.0.0] - 2026-02-04

### Initial Release

- Initial project structure.
- Multi-driver support (HttpHeader, IpApi, IpData, IpInfo, GeoPlugin, MaxMind).
- Advanced caching mechanism.
- Intelligent driver fallbacks.
- Geo-utilities (distance calculation).
- Bot detection and filtering.
- Blade directives for easy location display.
- Validation rules for location-aware requests.
- Comprehensive documentation.
