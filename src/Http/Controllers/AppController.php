<?php

namespace Sas\ShopwareAppLaravelSdk\Http\Controllers;

use Http\Discovery\Psr17Factory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\ResponseInterface;

class AppController extends Controller
{
    protected function getAppName(): string
    {
        return Config::get('sw-app.app_name');
    }

    protected function getAppSecret(): string
    {
        return Config::get('sw-app.app_secret');
    }

    protected function createResponse(array $data): ResponseInterface
    {
        $psr = new Psr17Factory();

        return $psr->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($psr->createStream(json_encode($data, JSON_THROW_ON_ERROR)));
    }
}
