<?php

namespace Sas\ShopwareAppLaravelSdk\Http\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sas\ShopwareAppLaravelSdk\Events\ConfigChangedEvent;
use Shopware\App\SDK\Context\ContextResolver;
use Shopware\App\SDK\Shop\ShopInterface;

class AppWebhookController extends AppController
{
    /**
     * @throws \JsonException
     */
    public function configChanged(ServerRequestInterface $request, ShopInterface $shop): ResponseInterface
    {
        $contextResolver = new ContextResolver();

        $webhook = $contextResolver->assembleWebhook($request, $shop);

        event(new ConfigChangedEvent($request, $shop, $webhook));

        return $this->createResponse([]);
    }
}
