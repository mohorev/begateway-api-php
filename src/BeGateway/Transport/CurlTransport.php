<?php

namespace BeGateway\Transport;

use BeGateway\Contract\GatewayTransport;
use BeGateway\Contract\Request;
use BeGateway\Traits\IdempotentRequest;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CurlTransport implements GatewayTransport, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @inheritdoc
     */
    public function send($shopId, $shopKey, Request $request)
    {
        $host = $request->endpoint();

        $ch = curl_init();

        $this->logger->debug(sprintf('host: %s', $request->endpoint()));
        $this->logger->debug(sprintf('shop id %s key %s', $shopId, $shopKey));

        if ($request->data() !== null) {
            $json = json_encode($request->data());

            $this->logger->debug(sprintf('with message %s', $json));
        }

        $options = [
            CURLOPT_URL => $host,
            CURLOPT_USERPWD =>  $shopId . ':' . $shopKey,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ];

        if ($request instanceof IdempotentRequest) {
            $options[CURLOPT_HTTPHEADER][] = sprintf('RequestID: %s', $request->getId());
        }

        if (!empty($json)) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-type: application/json';
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $json;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception('cURL error ' . curl_error($ch));
        }

        curl_close($ch);

        $this->logger->debug(sprintf('Response %s', $response));

        return $response;
    }
}
