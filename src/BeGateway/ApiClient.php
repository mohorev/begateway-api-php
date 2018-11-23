<?php

namespace BeGateway;

use BeGateway\Contract\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \BeGateway\Contract\GatewayTransport
     */
    private $transport;

    public function __construct(GatewayTransport $transport)
    {
        $this->transport = $transport;
    }

    public function send(Request $request)
    {
        return $this->transport->send($request);
    }

    public function sendIdempotent(Request $request, $id)
    {
        return $this->transport->send($request);
    }
}
