<?php

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use LucaDegasperi\OAuth2Server\Entities\Client;

class ClientRepository implements ClientRepositoryInterface
{

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param string $grantType The grant type used
     * @param null|string $clientSecret The client's secret (if sent)
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null)
    {
        $query = Client::where('identifier', $clientIdentifier);

        if (!is_null($clientSecret)) {
            $query->where('secret', $clientSecret);
        }

        return $query->first();
    }
}