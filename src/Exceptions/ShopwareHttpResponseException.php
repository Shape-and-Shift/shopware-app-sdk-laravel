<?php

namespace Sas\ShopwareAppLaravelSdk\Exceptions;

use Psr\Http\Message\ResponseInterface;

class ShopwareHttpResponseException extends \RuntimeException
{
    public function __construct(string $shopId, private readonly ResponseInterface $response, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Shopware Http response failed for shop %s', $shopId), 0, $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function context(): array
    {
        $response = $this->getResponse();

        return [
            'status_code' => $response->getStatusCode(),
            'payload' => json_decode($response->getBody()->getContents(), true),
        ];
    }
}
