# API Reference

## Facade: Location

Namespace: `Ermradulsharma\OmniLocate\Facades\Location`

### Location Methods

#### `get($ip = null)`

Attempt to retrieve the location of the user or a specific IP.

- **Parameters:**
  - `$ip` (string|null): The IP address to look up. Defaults to the client's IP.
- **Returns:** `Ermradulsharma\OmniLocate\Position` | `bool` (false on failure or bot)

#### `fallback(Driver $driver)`

Add a fallback driver at runtime.

- **Parameters:**
  - `$driver` (`Ermradulsharma\OmniLocate\Drivers\Driver`): The driver instance.
- **Returns:** `void`

#### `setDriver(Driver $driver)`

Set the current driver to use, overriding the default.

- **Parameters:**
  - `$driver` (`Ermradulsharma\OmniLocate\Drivers\Driver`): The driver instance.
- **Returns:** `void`

## Class: Position

Namespace: `Ermradulsharma\OmniLocate\Position`

Holds the location data retrieved from a driver.

### Properties

| Property       | Type     | Description                           |
| :------------- | :------- | :------------------------------------ |
| `ip`           | `string` | IP address used for lookup.           |
| `countryName`  | `string` | Full country name.                    |
| `countryCode`  | `string` | Two-letter ISO country code.          |
| `regionName`   | `string` | Region or state name.                 |
| `regionCode`   | `string` | Region code.                          |
| `cityName`     | `string` | City name.                            |
| `zipCode`      | `string` | Postal or ZIP code.                   |
| `latitude`     | `string` | Latitude.                             |
| `longitude`    | `string` | Longitude.                            |
| `currencyCode` | `string` | Currency code (derived from country). |
| `timezone`     | `string` | Timezone identifier.                  |
| `driver`       | `string` | Class name of the driver used.        |

### Position Methods

#### `distanceTo(Position $other, $unit = 'km')`

Calculate the distance to another Position object.

- **Parameters:**
  - `$other` (`Position`): The target position.
  - `$unit` (string): 'km' (default) or 'miles'.
- **Returns:** `float` | `null`

#### `flag()`

Get the country flag emoji based on `countryCode`.

- **Returns:** `string` | `null`

#### `isEmpty()`

Check if the position data is effectively empty (only contains IP and driver info).

- **Returns:** `bool`

#### `toArray()`

Get the instance data as an array.

- **Returns:** `array`
