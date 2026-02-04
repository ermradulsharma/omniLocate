<?php

namespace Ermradulsharma\OmniLocate;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class LocationServiceProvider extends ServiceProvider
{
    /**
     * Run boot operations.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLumen()) {
            return;
        }

        $config = __DIR__ . '/../config/config.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([$config => config_path('location.php')], 'config');

            $this->commands([\Ermradulsharma\OmniLocate\Commands\UpdateMaxMindDatabase::class]);

            if (! class_exists('CreateLocationsTable')) {
                $stub = __DIR__ . '/../database/migrations/create_locations_table.php.stub';

                $target = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_locations_table.php');

                $this->publishes([$stub => $target], 'migrations');
            }
        }

        $this->mergeConfigFrom($config, 'location');

        $this->registerBladeDirectives();

        $this->registerValidationRules();
    }

    /**
     * Register the validation rules.
     *
     * @return void
     */
    protected function registerValidationRules()
    {
        if (! $this->app->has('validator')) {
            return;
        }

        $this->app['validator']->extend('location', function ($attribute, $value, $parameters, $validator) {
            return (new \Ermradulsharma\OmniLocate\Rules\LocationRule($parameters[0] ?? ''))->passes($attribute, $value);
        });
    }

    /**
     * Register the blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        if (! $this->app->has('blade.compiler')) {
            return;
        }

        $this->app['blade.compiler']->directive('location', function ($expression) {
            return "<?php if (\$position = \Ermradulsharma\OmniLocate\Facades\Location::get()): ?>
                <?php echo \$position->{$expression} ?? \$position; ?>
            <?php endif; ?>";
        });
    }

    /**
     * Register the location binding.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('location', function ($app) {
            return new Location($app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['location'];
    }

    /**
     * Determine if the current application is Lumen.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen');
    }
}
