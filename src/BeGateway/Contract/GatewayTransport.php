<?php

namespace BeGateway\Contract;

interface GatewayTransport
{
    /**
     * @param Request $request
     * @return Response
     */
    public function send(Request $request);
}
