<?php

namespace LucaDegasperi\OAuth2Server\Storage;

use Carbon\Carbon;
use League\OAuth2\Server\Storage\MacTokenInterface;

/**
 * This is the fluent MAC token class.
 */
class FluentMacToken extends AbstractFluentAdapter implements MacTokenInterface
{
    /**
     * Get a MAC key by access token.
     *
     * @see League\OAuth2\Server\Storage\MacTokenInterface::class,
     *
     * @param string $accessToken
     *
     * @return string
     */
    public function getByAccessToken($accessToken)
    {
        $result = $this->getConnection()->table('oauth_mac_keys')
                ->where('access_token_id', $accessToken)
                ->first();

        if (is_null($result)) {
            return;
        }

        return $result->mac_key;
    }

    /**
     * Create a MAC key linked to an access token.
     *
     * @see League\OAuth2\Server\Storage\MacTokenInterface::class,
     *
     * @param string $macKey
     * @param string $accessToken
     */
    public function create($macKey, $accessToken)
    {
        $this->getConnection()->table('oauth_mac_keys')->insert([
            'access_token_id' => $accessToken,
            'mac_key' => $macKey,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
