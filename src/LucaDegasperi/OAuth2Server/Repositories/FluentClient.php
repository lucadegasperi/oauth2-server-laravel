<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\ClientInterface;
use DB;

class FluentClient implements ClientInterface
{
    protected $limitClientsToGrants = false;

    public function __construct($limitClientsToGrants) {
        $this->limitClientsToGrants = $limitClientsToGrants;
    }

    public function areClientsLimitedToGrants()
    {
        return $this->limitClientsToGrants;
    }

    public function limitClientsToGrants($limit = false)
    {
        $this->limitClientsToGrants = $limit;
    }

    public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $query = null;
        
        if (! is_null($redirectUri) && is_null($clientSecret)) {
            $query = DB::table('oauth_clients')
                        ->join('oauth_client_endpoints', 'oauth_clients.id', '=', 'oauth_client_endpoints.client_id')
                        ->where('oauth_clients.id', $clientId)
                        ->where('oauth_client_endpoints.redirect_uri', $redirectUri);
        } elseif (! is_null($clientSecret) && is_null($redirectUri)) {
            $query = DB::table('oauth_clients')
                        ->where('oauth_clients.id', $clientId)
                        ->where('oauth_clients.secret', $clientSecret);
        } elseif (! is_null($clientSecret) && ! is_null($redirectUri)) {
            $query = DB::table('oauth_clients')
                        ->join('oauth_client_endpoints', 'oauth_clients.id', '=', 'oauth_client_endpoints.client_id')
                        ->where('oauth_clients.id', $clientId)
                        ->where('oauth_clients.secret', $clientSecret)
                        ->where('oauth_client_endpoints.redirect_uri', $redirectUri);
        }

        if ($this->limitClientsToGrants === true and ! is_null($grantType)) {
            $query = $query->join('oauth_client_grants', 'oauth_clients.id', '=', 'oauth_client_grants.client_id')
                           ->join('oauth_grants', 'oauth_grants.id', '=', 'oauth_client_grants.grant_id')
                           ->where('oauth_grants.grant', $grantType);

        }

        $result = $query->first();

        if (is_null($result)) {
            return false;
        }

        $metadata = DB::table('oauth_client_metadata')->where('client_id', '=', $result->id)->lists('value', 'key');

        return array(
            'client_id'     =>  $result->id,
            'client_secret' =>  $result->secret,
            'redirect_uri'  =>  (isset($result->redirect_uri)) ? $result->redirect_uri : null,
            'name'          =>  $result->name,
            'metadata'      =>  $metadata
        );
    }
}
