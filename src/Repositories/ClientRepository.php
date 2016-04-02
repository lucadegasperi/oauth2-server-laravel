<?php
/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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