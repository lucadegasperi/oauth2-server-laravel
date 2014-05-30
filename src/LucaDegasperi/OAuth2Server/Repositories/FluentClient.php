<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\ClientInterface;
use DB;
use Config;

class FluentClient implements ClientInterface
{
    /**
     * Validate a client
     *
     * Example SQL query:
     *
     * <code>
     * # Client ID + redirect URI
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name
     *  FROM oauth_clients LEFT JOIN oauth_client_endpoints ON oauth_client_endpoints.client_id = oauth_clients.id
     *  WHERE oauth_clients.id = :clientId AND oauth_client_endpoints.redirect_uri = :redirectUri
     *
     * # Client ID + client secret
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_clients.name FROM oauth_clients WHERE
     *  oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret
     *
     * # Client ID + client secret + redirect URI
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name FROM
     *  oauth_clients LEFT JOIN oauth_client_endpoints ON oauth_client_endpoints.client_id = oauth_clients.id
     *  WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret AND
     *  oauth_client_endpoints.redirect_uri = :redirectUri
     * </code>
     *
     * Response:
     *
     * <code>
     * Array
     * (
     *     [client_id] => (string) The client ID
     *     [client secret] => (string) The client secret
     *     [redirect_uri] => (string) The redirect URI used in this request
     *     [name] => (string) The name of the client
     * )
     * </code>
     *
     * @param  string     $clientId     The client's ID
     * @param  string     $clientSecret The client's secret (default = "null")
     * @param  string     $redirectUri  The client's redirect URI (default = "null")
     * @param  string     $grantType    The grant type used in the request (default = "null")
     * @return bool|array               Returns false if the validation fails, array on success
     */
    public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $query = null;
        
        if (! is_null($redirectUri) && is_null($clientSecret)) {
            $query = DB::table('oauth_clients')
                        ->select(
                            'oauth_clients.id as id',
                            'oauth_clients.secret as secret',
                            'oauth_client_endpoints.redirect_uri as redirect_uri',
                            'oauth_clients.name as name')
                        ->join('oauth_client_endpoints', 'oauth_clients.id', '=', 'oauth_client_endpoints.client_id')
                        ->where('oauth_clients.id', $clientId)
                        ->where('oauth_client_endpoints.redirect_uri', $redirectUri);
        } elseif (! is_null($clientSecret) && is_null($redirectUri)) {
            $query = DB::table('oauth_clients')
                        ->select(
                            'oauth_clients.id as id',
                            'oauth_clients.secret as secret',
                            'oauth_clients.name as name')
                        ->where('oauth_clients.id', $clientId)
                        ->where('oauth_clients.secret', $clientSecret);
        } elseif (! is_null($clientSecret) && ! is_null($redirectUri)) {
            $query = DB::table('oauth_clients')
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

        if (Config::get('lucadegasperi/oauth2-server-laravel::oauth2.limit_clients_to_grants') === true and ! is_null($grantType)) {
            $query = $query->join('oauth_client_grants', 'oauth_clients.id', '=', 'oauth_client_grants.client_id')
                           ->join('oauth_grants', 'oauth_grants.id', '=', 'oauth_client_grants.grant_id')
                           ->where('oauth_grants.grant', $grantType);

        }

        $result = $query->first();

        if (is_null($result)) {
            return false;
        }

        $result = (object) $result;

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
