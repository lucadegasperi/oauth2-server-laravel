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

namespace LucaDegasperi\OAuth2Server\Storage;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Entity\ClientEntity;
use Carbon\Carbon;

class FluentClient extends FluentAdapter implements ClientInterface
{
    /**
     * @var bool
     */
    protected $limitClientsToGrants = false;

    /**
     * @param Resolver $connection
     * @param bool $limitClientsToGrants
     */
    public function __construct(Resolver $resolver, $limitClientsToGrants = false)
    {
        parent::__construct($resolver);
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
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
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

        return $this->hydrateEntity($result);
    }

    /**
     * Get the client associated with a session
     * @param  \League\OAuth2\Server\Entity\SessionEntity $session The session
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
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

        return $this->hydrateEntity($result);
    }

    /**
     * @param string $name The client's unique name
     * @param string $id The client's unique id
     * @param string $secret The clients' unique secret
     * @return int
     */
    public function create($name, $id, $secret)
    {
        return $this->getConnection()->table('oauth_clients')->insertGetId([
            'id'  => $id,
            'name' => $name,
            'secret'   => $secret,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * @param $result
     * @return \League\OAuth2\Server\Entity\ClientEntity
     */
    protected function hydrateEntity($result)
    {
        $client = new ClientEntity($this->getServer());
        $client->hydrate([
            'id' => $result->id,
            'name' => $result->name,
            'secret' => $result->secret,
            'redirectUri' => (isset($result->redirect_uri) ? $result->redirect_uri : null)
        ]);
        return $client;
    }
}
