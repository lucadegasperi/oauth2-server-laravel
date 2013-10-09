<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\ScopeInterface;
use DB;
use Config;

class FluentScope implements ScopeInterface
{

    public function getScope($scope, $clientId = null, $grantType = null)
    {
         $query = DB::table('oauth_scopes')
                    ->where('oauth_scopes.scope', $scope);

        if (Config::get('oauth2-server-laravel::oauth2.limit_clients_to_scopes') === true and ! is_null($clientId)) {
            $query = $query->join('oauth_client_scopes', 'oauth_scopes.id', '=', 'oauth_client_scopes.scope_id')
                           ->where('oauth_client_scopes.client_id', $clientId);
        }

        if (Config::get('oauth2-server-laravel::oauth2.limit_scopes_to_grants') === true and ! is_null($grantType)) {
            $query = $query->join('oauth_grant_scopes', 'oauth_scopes.id', '=', 'oauth_grant_scopes.scope_id')
                           ->join('oauth_grants', 'oauth_grants.id', '=', 'oauth_grant_scopes.grant_id')
                           ->where('oauth_grants.grant', $grantType);
        }


        $result = $query->first();

        if (is_null($result)) {
            return false;
        }

        return array(
            'id'          => $result->id,
            'scope'       => $result->scope,
            'name'        => $result->name,
            'description' => $result->description
        );
    }
}
