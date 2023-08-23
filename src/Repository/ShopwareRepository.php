<?php

namespace Sas\ShopwareAppLaravelSdk\Repository;

use Illuminate\Support\Str;
use Psr\Http\Client\ClientExceptionInterface;
use Sas\ShopwareAppLaravelSdk\Data\Criteria;
use Sas\ShopwareAppLaravelSdk\Trait\ShopwareClient;

abstract class ShopwareRepository
{
    use ShopwareClient;

    private function getEntityEndpoint(): string
    {
        return Str::of($this->getEntityName())->replace('_', '-')->toString();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function search(Criteria $criteria): ?array
    {
        return $this->swPostRequest(
            sprintf('/api/search/%s', $this->getEntityEndpoint()),
            $criteria->parse(),
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(array $data): ?array
    {
        return $this->swPostRequest(
            sprintf('/api/%s', $this->getEntityEndpoint()),
            $data,
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $id, array $data): ?array
    {
        return $this->swPatchRequest(
            sprintf('/api/%s/%s', $this->getEntityEndpoint(), $id),
            array_merge([
                'id' => $id,
            ], $data),
        );
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function delete(string $id): ?array
    {
        return $this->swDeleteRequest(
            sprintf('/api/%s/%s', $this->getEntityEndpoint(), $id),
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function sync(string $action, array $payload): ?array
    {
        return $this->swPostRequest(
            'api/_action/sync',
            [
                'entity' => $this->getEntityEndpoint(),
                'action' => $action,
                'payload' => $payload,
            ]
        );
    }

    abstract protected function getEntityName(): string;
}
