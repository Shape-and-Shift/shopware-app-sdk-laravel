<?php

namespace Sas\ShopwareAppLaravelSdk\Trait;

use Http\Discovery\Psr17Factory;
use Http\Discovery\Psr18Client;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sas\ShopwareAppLaravelSdk\Exceptions\ShopwareHttpResponseException;
use Shopware\App\SDK\Exception\MissingShopParameterException;
use Shopware\App\SDK\HttpClient\ClientFactory;
use Shopware\App\SDK\Shop\ShopInterface;

trait ShopwareClient
{
    protected function getShop(): ShopInterface
    {
        $shop = app()->get(ShopInterface::class);
        if (!$shop instanceof ShopInterface) {
            throw new MissingShopParameterException();
        }

        return $shop;
    }

    protected function createSwClient(): ClientInterface
    {
        $shop = $this->getShop();

        $clientFactory = new ClientFactory(
            cache()->store(),
            new Psr18Client(),
            logger()->getLogger(),
        );

        return $clientFactory->createClient($shop);
    }

    protected function createSwRequest(ShopInterface $shop, string $method, string $endpoint): RequestInterface
    {
        $factory = new Psr17Factory();

        return $factory->createRequest(
            $method,
            sprintf('%s/%s', rtrim($shop->getShopUrl(), '/'), ltrim($endpoint, '/'))
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    protected function swPostRequest(string $endpoint, array $data = []): ?array
    {
        $client = $this->createSwClient();
        $shop = $this->getShop();

        $factory = new Psr17Factory();
        $request = $this->createSwRequest($shop, 'POST', $endpoint);

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factory->createStream(json_encode($data, JSON_THROW_ON_ERROR)));

        return $this->parseSwResponse($shop, $client->sendRequest($request));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    protected function swPatchRequest(string $endpoint, array $data = []): ?array
    {
        $client = $this->createSwClient();
        $shop = $this->getShop();

        $factory = new Psr17Factory();
        $request = $this->createSwRequest($shop, 'PATCH', $endpoint);

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($factory->createStream(json_encode($data, JSON_THROW_ON_ERROR)));

        return $this->parseSwResponse($shop, $client->sendRequest($request));
    }

    /**
     * @throws ClientExceptionInterface
     */
    protected function swDeleteRequest(string $endpoint): ?array
    {
        $client = $this->createSwClient();
        $shop = $this->getShop();

        $request = $this->createSwRequest($shop, 'DELETE', $endpoint);

        return $this->parseSwResponse($shop, $client->sendRequest($request));
    }

    /**
     * @throws ClientExceptionInterface
     */
    protected function swGetRequest(string $endpoint): ?array
    {
        $client = $this->createSwClient();
        $shop = $this->getShop();

        $request = $this->createSwRequest($shop, 'GET', $endpoint);

        return $this->parseSwResponse($shop, $client->sendRequest($request));
    }

    private function parseSwResponse(ShopInterface $shop, ResponseInterface $response): ?array
    {
        if ($response->getStatusCode() >= 400) {
            throw new ShopwareHttpResponseException($shop->getShopId(), $response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
