<?php

use BeGateway\ApiClient;
use BeGateway\Request\QueryByPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

$token = $argv[1];
print 'Trying to Query by Payment token ' . $token . PHP_EOL;

$query = new QueryByPaymentToken;
$query->setToken($token);

$client = new ApiClient([
    'shop_id' => 361,
    'shop_key' => 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d',
    'language' => 'en',
    'test' => true,
]);

$response = $client->send($query);

print_r($response);
