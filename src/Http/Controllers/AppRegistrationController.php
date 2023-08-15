<?php

namespace Sas\ShopwareAppLaravelSdk\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sas\ShopwareAppLaravelSdk\Shop\ShopModel;
use Shopware\App\SDK\AppConfiguration;
use Shopware\App\SDK\AppLifecycle;
use Shopware\App\SDK\Registration\RegistrationService;
use Shopware\App\SDK\Shop\ShopResolver;

class AppRegistrationController extends Controller
{
    public function __construct(
        private readonly UrlGenerator $routing,
    ) {
    }

    public function register(ServerRequestInterface $request): ResponseInterface
    {
        $registration = $this->initLifecycle();

        return $registration->register($request);
    }

    public function confirm(ServerRequestInterface $request): ResponseInterface
    {
        $registration = $this->initLifecycle();

        return $registration->registerConfirm($request);
    }

    public function activate(ServerRequestInterface $request): ResponseInterface
    {
        $registration = $this->initLifecycle();

        return $registration->activate($request);
    }

    public function deactivate(ServerRequestInterface $request): ResponseInterface
    {
        $registration = $this->initLifecycle();

        return $registration->deactivate($request);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $registration = $this->initLifecycle();

        return $registration->delete($request);
    }

    protected function getAppName(): string
    {
        return Config::get('sw-app.app_name');
    }

    protected function getAppSecret(): string
    {
        return Config::get('sw-app.app_secret');
    }

    protected function getConfirmationUrl(): string
    {
        return $this->routing->route('sw-app.auth.confirmation');
    }

    private function initLifecycle(): AppLifecycle
    {
        $shop = new ShopModel();

        $app = new AppConfiguration(
            $this->getAppName(),
            $this->getAppSecret(),
            $this->getConfirmationUrl(),
        );

        $registrationService = new RegistrationService($app, $shop);
        $shopResolver = new ShopResolver($shop);

        return new AppLifecycle($registrationService, $shopResolver, $shop);
    }
}
