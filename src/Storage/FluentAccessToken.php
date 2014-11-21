<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Access Token
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use League\OAuth2\Server\Entity\AbstractTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use Carbon\Carbon;

class FluentAccessToken extends FluentAdapter implements AccessTokenInterface
{
    /**
      * {@inheritdoc}
      */
    public function get($token)
    {
        $result = $this->getConnection()->table('oauth_access_tokens')
                ->where('oauth_access_tokens.id', $token)
                ->first();

        if (is_null($result)) {
            return null;
        }

        return (new AccessTokenEntity($this->getServer()))
               ->setId($result->id)
               ->setExpireTime((int)$result->expire_time);
    }

    /**
      * {@inheritdoc}
      */
    public function getScopes(AccessTokenEntity $token)
    {
        $result = $this->getConnection()->table('oauth_access_token_scopes')
                ->select('oauth_scopes.*')
                ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
                ->where('oauth_access_token_scopes.access_token_id', $token->getId())
                ->get();
        
        $scopes = [];
        
        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
               'id' => $scope->id,
                'description' => $scope->description
            ]);
        }
        
        return $scopes;
    }

    /**
      * {@inheritdoc}
      */
    public function create($token, $expireTime, $sessionId)
    {
        $this->getConnection()->table('oauth_access_tokens')->insert([
            'id' => $token,
            'expire_time' => $expireTime,
            'session_id' => $sessionId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return (new AccessTokenEntity($this->getServer()))
               ->setId($token)
               ->setExpireTime((int)$expireTime);
    }

    /**
      * {@inheritdoc}
      */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $this->getConnection()->table('oauth_access_token_scopes')->insert([
            'access_token_id' => $token->getId(),
            'scope_id'        => $scope->getId(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now()
        ]);
    }

    /**
      * {@inheritdoc}
      */
    public function delete(AccessTokenEntity $token)
    {
        $this->getConnection()->table('oauth_access_tokens')
        ->where('oauth_access_tokens.id', $token->getId())
        ->delete();
    }
}
