<?php

namespace Sas\ShopwareAppLaravelSdk\Shop;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Shopware\App\SDK\Shop\ShopInterface;
use Shopware\App\SDK\Shop\ShopRepositoryInterface;

/**
 * @property string $shop_id
 * @property string $shop_url
 * @property string $shop_secret
 * @property string $api_key
 * @property string $secret_key
 * @property bool $active
 */
class ShopModel extends Model implements ShopRepositoryInterface
{
    use HasUuids;

    protected $table = 'sw_shops';

    protected $fillable = [
        'shop_id',
        'shop_url',
        'shop_secret',
        'api_key',
        'secret_key',
        'active',
    ];

    protected $hidden = [
        'shop_secret',
        'api_key',
        'secret_key',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function createShopStruct(string $shopId, string $shopUrl, string $shopSecret): ShopInterface
    {
        return new Shop($shopId, $shopUrl, $shopSecret);
    }

    public function createShop(ShopInterface $shop): void
    {
        $updatedData = array_merge(
            array_filter([
                'shop_url' => $shop->getShopUrl(),
                'shop_secret' => $shop->getShopSecret(),
                'api_key' => $shop->getShopClientId(),
                'secret_key' => $shop->getShopClientSecret(),
            ]),
            [
                'active' => $shop->isShopActive(),
            ]
        );

        self::updateOrCreate(
            ['shop_id' => $shop->getShopId()],
            $updatedData,
        );
    }

    public function getShopFromId(string $shopId): ?ShopInterface
    {
        $query = self::where('shop_id', $shopId);

        $shopModel = $query->first();
        if (!$shopModel instanceof ShopModel) {
            return null;
        }

        return new Shop(
            $shopId,
            $shopModel->shop_url,
            $shopModel->shop_secret,
            $shopModel->active,
            $shopModel->api_key,
            $shopModel->secret_key,
        );
    }

    public function updateShop(ShopInterface $shop): void
    {
        $this->createShop($shop);
    }

    public function deleteShop(string $shopId): void
    {
        self::where('shop_id', $shopId)->delete();
    }
}
