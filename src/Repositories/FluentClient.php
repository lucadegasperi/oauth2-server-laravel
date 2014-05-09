<?php
/**
 * Fluent storage implementation for an OAuth 2.0 Client
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Entity\ClientEntity;

class FluentClient extends FluentAdapter implements ClientInterface
{
    protected $limitClientsToGrants = false;

    /**
     * @param bool $limitClientsToGrants
     */
    public function __construct($limitClientsToGrants = false)
    {
        $this->limitClientsToGrants = $limitClientsToGrants;
    }

    /**
     * @return bool
     */
    public function areClientsLimitedToGrants()
    {
        return $this->limitClientsToGrants;
    }

    /**
     * @param bool $limit whether or not to limit clients to grants
     */
    public function limitClientsToGrants($limit = false)
    {
        $this->limitClientsToGrants = $limit;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $grantType
     * @return null|\League\OAuth2\Server\Entity\Client
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $query = null;
        
        if (! is_null($redirectUri) && is_null($clientSecret)) {
            $query = $this->getConnection()->table('oauth_clients')
                   ->select(
                       'oauth_clients.id as id',
                       'oauth_clients.secret as secret',
                       'oauth_client_endpoints.redirect_uri as redirect_uri',
                       'oauth_clients.name as name')
                   ->join('oauth_client_endpoints', 'oauth_clients.id', '=', 'oauth_client_endpoints.client_id')
                   ->where('oauth_clients.id', $clientId)
                   ->where('oauth_client_endpoints.redirect_uri', $redirectUri);
        } elseif (! is_null($clientSecret) && is_null($redirectUri)) {
            $query = $this->getConnection()->table('oauth_clients')
                   ->select(
                       'oauth_clients.id as id',
                       'oauth_clients.secret as secret',
                       'oauth_clients.name as name')
                   ->where('oauth_clients.id', $clientId)
                   ->where('oauth_clients.secret', $clientSecret);
        } elseif (! is_null($clientSecret) && ! is_null($redirectUri)) {
            $query = $this->getConnection()->table('oauth_clients')
                   ->select(
                       'oauth_clients.id as id',
                       'oauth_clients.secret as secret',
                       'oauth_client_endpoints.redirect_uri as redirect_uri',
                       'oauth_clients.name as name')
                   ->join('oauth_client_endpoints', 'oauth_clients.id', '=', 'oauth_client_endpoints.client_id')
                   ->where('oauth_clients.id', $clientId)
                   ->where('oauth_clients.secret', $clientSecret)
                   ->where('oauth_client_endpoints.redirect_uri', $redirectUri);
        }

        if ($this->limitClientsToGrants === true and ! is_null($grantType)) {
            $query = $query->join('oauth_client_grants', 'oauth_clients.id', '=', 'oauth_client_grants.client_id')
                   ->join('oauth_grants', 'oauth_grants.id', '=', 'oauth_client_grants.grant_id')
                   ->where('oauth_grants.id', $grantType);
        }

        $result = $query->first();

        if (is_null($result)) {
            return null;
        }

        return $this->createClient($result);
    }

    /**
     * Get the client associated with a session
     * @param  \League\OAuth2\Server\Entity\SessionEntity $session The session
     * @return null|\League\OAuth2\Server\Entity\Client
     */
    public function getBySession(SessionEntity $session)
    {
        $result = $this->getConnection()->table('oauth_clients')
                ->select(
                    'oauth_clients.id as id',
                    'oauth_clients.secret as secret',
                    'oauth_clients.name as name')
                ->join('oauth_sessions', 'oauth_sessions.client_id', '=', 'oauth_clients.id')
                ->where('oauth_sessions.id', '=', $session->getId())
                ->first();

        if (is_null($result)) {
            return null;
        }

        return $this->createClient($result);
    }

    /**
     * @param $result
     * @return \League\OAuth2\Server\Entity\Client
     */
    protected function createClient($result)
    {
        return (new ClientEntity($this->getServer()))
            ->setId($result->id)
            ->setSecret($result->secret)
            ->setName($result->name)
            ->setRedirectUri(isset($result->redirect_uri) ? $result->redirect_uri : null);
    }
}
