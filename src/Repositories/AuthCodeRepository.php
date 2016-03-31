<?php

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\Interfaces\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use LucaDegasperi\OAuth2Server\Entities\AuthCode;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param \League\OAuth2\Server\Entities\Interfaces\AuthCodeEntityInterface $authCodeEntity
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCodeEntity->save();
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId)
    {
        AuthCode::where('code', $codeId)->delete();
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isAuthCodeRevoked($codeId)
    {
        return AuthCode::where('code', $codeId)->count() === 0;
    }
}