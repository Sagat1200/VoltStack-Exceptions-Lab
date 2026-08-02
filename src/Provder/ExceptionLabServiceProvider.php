<?php

declare(strict_types=1);

namespace VoltStack\ExceptionLab\Provider;

use Quantum\View\ViewFactory;
use VoltStack\ExceptionLab\Service\Provider\Routes\ExceptionLabRouteService;
use VoltStack\Framework\ServiceProvider;

final class ExceptionLabServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('exception-lab', 'VoltStack\ExceptionLab\ExceptionLab');
    }

    public function boot(): void
    {
        $this->registerViewPaths();

        $enableDemoRouteRoutes = (bool) config(
            'exception-lab.enable_demo_route_routes',
            in_array($this->app->environment(), ['local', 'testing'], true)
        );

        if ($enableDemoRouteRoutes) {
            ExceptionLabRouteService::registerExceptionLabRoutes();
        }
    }

    private function registerViewPaths(): void
    {
        $resourcePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources';

        if (! is_dir($resourcePath)) {
            return;
        }

        $views = $this->app->make(ViewFactory::class);
        $paths = $views->paths();

        if (in_array($resourcePath, $paths, true)) {
            return;
        }

        $paths[] = $resourcePath;
        $views->setPaths($paths);
    }
}