<?php

namespace BeGateway\Tests;

use BeGateway\ApiClient;

class TestCase extends \PHPUnit\Framework\TestCase
{
    const SHOP_ID = 361;
    const SHOP_KEY = 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d';
    const SHOP_PUB_KEY = 'cc803ec0-6038-4fe6-abf0-a514d5e89d6f';

    const SHOP_ID_3D = 362;
    const SHOP_KEY_3D = '9ad8ad735945919845b9a1996af72d886ab43d3375502256dbf8dd16bca59a4e';
    const SHOP_PUB_KEY_3D = 'ee7257d4-dcff-41bf-a95f-fe0ff79bf64f';

    protected $shopId = 0;
    protected $shopKey = 'secret';
    protected $shopPubKey;

    public function authorize($secure3D = false)
    {
        $shopId = null;
        $shopKey = null;

        if ($secure3D) {
            $shopId = getenv('SHOP_ID_3D');

            if (!$shopId) {
                $shopId = self::SHOP_ID_3D;
            }

            $shopKey = getenv('SHOP_SECRET_KEY_3D');
            if (!$shopKey) {
                $shopKey = self::SHOP_KEY_3D;
            }

            $shop_pub_key = getenv('SHOP_PUB_KEY_3D');
            if (!$shop_pub_key) {
                $shop_pub_key = self::SHOP_PUB_KEY_3D;
            }
        } else {
            $shopId = getenv('SHOP_ID');

            if (!$shopId) {
                $shopId = self::SHOP_ID;
            }

            $shopKey = getenv('SHOP_SECRET_KEY');
            if (!$shopKey) {
                $shopKey = self::SHOP_KEY;
            }
            $shop_pub_key = getenv('SHOP_PUB_KEY');
            if (!$shop_pub_key) {
                $shop_pub_key = self::SHOP_PUB_KEY;
            }
        }

        $this->shopId = $shopId;
        $this->shopKey = $shopKey;
        $this->shopPubKey = $shop_pub_key;
    }

    protected function getApiClient()
    {
        return new ApiClient([
            'shop_id' => $this->shopId,
            'shop_key' => $this->shopKey,
        ]);
    }
}
