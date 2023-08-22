<?php

namespace Sas\ShopwareAppLaravelSdk\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\App\SDK\Context\Webhook\WebhookAction;
use Shopware\App\SDK\Shop\ShopInterface;

class ConfigChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ServerRequestInterface $request,
        public ShopInterface $shop,
        public WebhookAction $webhookAction,
    ) {
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    public function getWebhookAction(): WebhookAction
    {
        return $this->webhookAction;
    }
}
