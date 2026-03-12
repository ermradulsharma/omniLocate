<?php

declare(strict_types=1);

namespace Skywalker\Location;

use Skywalker\Location\Commands\UpdateMaxMindDatabase;
use Skywalker\Location\Facades\Location as LocationFacade;
use Skywalker\Location\Rules\LocationRule;
use Skywalker\Support\Providers\PackageServiceProvider;

class LocationServiceProvider extends PackageServiceProvider
{
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'location';

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        parent::boot();

        if ($this->app->runningInConsole()) {
            $this->publishAll();
        }

        if (config('location.dashboard.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/routes.php');
        }

        $this->registerValidationRules();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->app->singleton('location', function ($app) {
            return new Location($app['config']);
        });

        $this->registerCommands([
            UpdateMaxMindDatabase::class,
        ]);
    }

    /**
     * Register the validation rules.
     */
    protected function registerValidationRules(): void
    {
        /** @var \Illuminate\Validation\Factory $validator */
        $validator = $this->app->make('validator');
        $validator->extend('location', function ($attribute, $value, $parameters) {
            return (new LocationRule($parameters[0] ?? ''))->passes($attribute, $value);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['location'];
    }

    /**
     * Register the package's custom blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        parent::registerBladeDirectives();

        /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
        $blade = $this->app->make('blade.compiler');
        $blade->directive('location', function ($expression) {
            return "<?php if ((\$position = \Skywalker\Location\Facades\Location::get()) instanceof \Skywalker\Location\DataTransferObjects\Position): ?>
                <?php echo \$position->{$expression} ?? \$position; ?>
            <?php endif; ?>";
        });
    }
}
