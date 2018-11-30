<?php

use BeGateway\ApiClient;
use BeGateway\Request\QueryByPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

$token = $argv[1];
print 'Trying to Query by Payment token ' . $token . PHP_EOL;

$query = new QueryByPaymentToken;
$query->setToken($token);

$client = new ApiClient([
    'language' => 'en',
    'test' => true,
]);

$response = $client->send($query);

print_r($response);
