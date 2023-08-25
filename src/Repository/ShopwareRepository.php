<?php

namespace Sas\ShopwareAppLaravelSdk\Repository;

use Illuminate\Support\Arr;
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
        $response = $this->swPostRequest(
            sprintf('/api/search/%s', $this->getEntityEndpoint()),
            $criteria->parse(),
        );

        return $this->hydrateSearchResult($response);
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

    private function hydrateSearchResult(?array $response): ?array
    {
        if (empty($response)) {
            return null;
        }

        $response['data'] = Arr::map(
            $response['data'] ?? [],
            fn (mixed $item) => $this->hydrateEntity(
                $item,
                $response['included'] ?? [],
            ),
        );

        return $response;
    }

    private function hydrateEntity(array $row, array $includes): array
    {
        $id = $row['id'] ?? null;
        $relationships = $row['relationships'] ?? [];

        $data = [];
        foreach ($relationships as $property => $relationship) {
            $relationshipData = $relationship['data'] ?? [];
            if (empty($relationshipData)) {
                continue;
            }

            if ($property === 'extensions') {
                $data[$property] = $this->hydrateExtensions($id, $includes);

                continue;
            }

            if (Arr::isAssoc($relationshipData)) {
                $nestedEntity = $this->hydrateToOne($relationshipData, $includes);

                if (!empty($nestedEntity)) {
                    $data[$property] = $nestedEntity;
                }

                continue;
            }

            $data[$property] = $this->hydrateToMany($relationshipData, $includes);
        }

        $row['hydrate'] = $data;

        return $row;
    }

    private function getIncluded(string $entityName, string $id, array $includes): ?array
    {
        return Arr::first(
            $includes,
            fn (mixed $item) => ($item['id'] ?? null) === $id && ($item['type'] ?? null) === $entityName
        );
    }

    private function hydrateExtensions(string $id, array $includes): array
    {
        $extension = $this->getIncluded('extension', $id, $includes);

        $row = $extension['attributes'] ?? [];
        $data = [];
        foreach ($extension['relationships'] ?? [] as $property => $relationship) {
            $relationshipData = $relationship['data'] ?? [];
            if (empty($relationshipData)) {
                continue;
            }

            if (Arr::isAssoc($relationshipData)) {
                $nestedEntity = $this->hydrateToOne($relationshipData, $includes);

                if (!empty($nestedEntity)) {
                    $data[$property] = $nestedEntity;
                }

                continue;
            }

            $data[$property] = $this->hydrateToMany($relationshipData, $includes);
        }

        $row['hydrate'] = $data;

        return $row;
    }

    private function hydrateToOne(?array $value, array $includes): ?array
    {
        $nestedRaw = $this->getIncluded($value['type'] ?? null, $value['id'] ?? null, $includes);

        if (empty($nestedRaw)) {
            return null;
        }

        return $this->hydrateEntity(
            $nestedRaw,
            $includes,
        );
    }

    private function hydrateToMany(?array $value, array $includes): array
    {
        $collection = [];
        if (empty($value)) {
            return $collection;
        }

        foreach ($value as $item) {
            $type = $item['type'] ?? null;
            $nestedRaw = $this->getIncluded($type, $item['id'] ?? null, $includes);
            if (empty($nestedRaw)) {
                continue;
            }

            $nestedEntity = $this->hydrateEntity(
                $nestedRaw,
                $includes,
            );

            if (empty($nestedEntity)) {
                continue;
            }

            $collection[] = $nestedEntity;
        }

        return $collection;
    }
}
