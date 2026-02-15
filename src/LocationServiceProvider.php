<?php

namespace Skywalker\Location;

use Illuminate\Support\Str;
use Skywalker\Support\Providers\PackageServiceProvider;

class LocationServiceProvider extends PackageServiceProvider
{
    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'skywalker';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'location';
    /**
     * Run boot operations.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if ($this->isLumen()) {
            return;
        }

        $this->publishAll();

        if ($this->app['config']->get('location.dashboard.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }

        $this->loadViews();

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
            return (new \Skywalker\Location\Rules\LocationRule($parameters[0] ?? ''))->passes($attribute, $value);
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
            return "<?php if (\$position = \Skywalker\Location\Facades\Location::get()): ?>
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
        parent::register();

        $this->registerConfig();

        $this->app->singleton('location', function ($app) {
            return new Location($app['config']);
        });

        $this->registerCommands([\Skywalker\Location\Commands\UpdateMaxMindDatabase::class]);
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

