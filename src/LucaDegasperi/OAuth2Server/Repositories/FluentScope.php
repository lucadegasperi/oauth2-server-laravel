<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\ScopeInterface;
use DB;

class FluentScope implements ScopeInterface
{
    protected $limitClientsToScopes = false;

    protected $limitScopesToGrants = false;

    public function __construct($limitClientsToScopes = false, $limitScopesToGrants = false)
    {
        $this->limitClientsToScopes = $limitClientsToScopes;
        $this->limitScopesToGrants = $limitScopesToGrants;
    }

    public function limitClientsToScopes($limit = false)
    {
        $this->limitClientsToScopes = $limit;
    }

    public function limitScopesToGrants($limit = false)
    {
        $this->limitScopesToGrants = $limit;
    }

    public function areClientsLimitedToScopes()
    {
        return $this->limitClientsToScopes;
    }

    public function areScopesLimitedToGrants()
    {
        return $this->limitScopesToGrants;
    }


    /**
     * Return information about a scope
     *
     * Example SQL query:
     *
     * <code>
     * SELECT * FROM oauth_scopes WHERE scope = :scope
     * </code>
     *
     * Response:
     *
     * <code>
     * Array
     * (
     *     [id] => (int) The scope's ID
     *     [scope] => (string) The scope itself
     *     [name] => (string) The scope's name
     *     [description] => (string) The scope's description
     * )
     * </code>
     *
     * @param  string     $scope     The scope
     * @param  string     $clientId  The client ID (default = "null")
     * @param  string     $grantType The grant type used in the request (default = "null")
     * @return bool|array If the scope doesn't exist return false
     */
    public function getScope($scope, $clientId = null, $grantType = null)
    {
         $query = DB::table('oauth_scopes')
                    ->select('oauth_scopes.id as id', 'oauth_scopes.scope as scope', 'oauth_scopes.name as name', 'oauth_scopes.description as description')
                    ->where('oauth_scopes.scope', $scope);

        if ($this->limitClientsToScopes === true and ! is_null($clientId)) {
            $query = $query->join('oauth_client_scopes', 'oauth_scopes.id', '=', 'oauth_client_scopes.scope_id')
                           ->where('oauth_client_scopes.client_id', $clientId);
        }

        if ($this->limitScopesToGrants === true and ! is_null($grantType)) {
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
