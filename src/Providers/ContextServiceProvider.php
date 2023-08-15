<?php

namespace Sas\ShopwareAppLaravelSdk\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use Sas\ShopwareAppLaravelSdk\Shop\ShopModel;
use Shopware\App\SDK\Shop\ShopInterface;
use Shopware\App\SDK\Shop\ShopResolver;

class ContextServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ShopInterface::class, function (Application $app) {
            $request = $app->get(ServerRequestInterface::class);
            if (!$request instanceof ServerRequestInterface) {
                throw new \RuntimeException('Invalid request exception');
            }

            $shopResolver = new ShopResolver(new ShopModel());

            return $shopResolver->resolveShop($request);
        });
    }
}
