<?php

namespace BeGateway\Contract;

interface GatewayTransport
{
    /**
     * @param int $shopId
     * @param string $shopKey
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function send($shopId, $shopKey, Request $request);
}
