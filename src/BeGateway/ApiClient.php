<?php

namespace BeGateway;

use BeGateway\Contract\Request;

class ApiClient
{
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
