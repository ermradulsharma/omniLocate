# Usage

## Basic Detection

### Detect Current Visitor

To detect the location of the current user based on their IP address:

```php
use Ermradulsharma\OmniLocate\Facades\Location;

if ($position = Location::get()) {
    // Successfully detected
    echo $position->countryName;
} else {
    // Failed to detect or user is a bot
}
```

### Detect Specific IP

You can pass a specific IP address to the `get` method:

```php
$position = Location::get('8.8.8.8');
```

## Accessing Position Data

The `Location::get()` method returns a `Ermradulsharma\OmniLocate\Position` object with the following public properties:

- `ip`: The IP address used.
- `countryName`: Full name of the country (e.g., "United States").
- `countryCode`: Two-letter country code (e.g., "US").
- `regionName`: Name of the region/state (e.g., "California").
- `regionCode`: Region code (e.g., "CA").
- `cityName`: City name (e.g., "Mountain View").
- `zipCode`: Postal/Zip code.
- `latitude`: Latitude.
- `longitude`: Longitude.
- `currencyCode`: Currency code (e.g., "USD").
- `timezone`: Timezone (e.g., "America/Los_Angeles").
- `driver`: The driver used to retrieve the data.

### Helper Methods

**Get Flag Emoji:**

```php
echo $position->flag(); // 🇺🇸
```

**Check if Empty:**

```php
if ($position->isEmpty()) { ... }
```

## Blade Directives

You can easily display location information in your Blade templates using the `@location` directive.

```blade
<p>You are visiting from: @location('cityName'), @location('countryName')</p>
<p>Currency: @location('currencyCode')</p>
```

If the location is not available, nothing will be displayed.

## Distance Utilities

Calculate the distance between two `Position` objects.

```php
$position1 = Location::get('8.8.8.8');
$position2 = Location::get('1.1.1.1');

if ($position1 && $position2) {
    $km = $position1->distanceTo($position2); // Default is Kilometers
    $miles = $position1->distanceTo($position2, 'miles');
}
```

## Validation Rules

OmniLocate provides a validation rule to restrict actions based on location.

```php
$request->validate([
    'signup_ip' => 'required|location:India,United States',
]);
```

This ensures the `signup_ip` resolves to a location within India or the United States.
