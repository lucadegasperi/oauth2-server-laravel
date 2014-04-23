<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\Scope;
use DB;

class FluentScope extends Adapter implements ScopeInterface
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
     * @param  string     $scope     The scope
     * @param  string     $grantType The grant type used in the request (default = "null")
     * @return bool|array If the scope doesn't exist return false
     */
    public function get($scope, $grantType = null)
    {
         $query = DB::table('oauth_scopes')
                    ->select('oauth_scopes.scope as scope', 'oauth_scopes.description as description')
                    ->where('oauth_scopes.scope', $scope);

        // TODO: allow for client scopes limiting
        /*if ($this->limitClientsToScopes === true and ! is_null($clientId)) {
            $query = $query->join('oauth_client_scopes', 'oauth_scopes.id', '=', 'oauth_client_scopes.scope_id')
                           ->where('oauth_client_scopes.client_id', $clientId);
        }*/

        if ($this->limitScopesToGrants === true and ! is_null($grantType)) {
            $query = $query->join('oauth_grant_scopes', 'oauth_scopes.scope', '=', 'oauth_grant_scopes.scope')
                           ->join('oauth_grants', 'oauth_grants.grant', '=', 'oauth_grant_scopes.grant')
                           ->where('oauth_grants.grant', $grantType);
        }

        $result = $query->first();

        if (is_null($result)) {
            return null;
        }

        return (new Scope($this->getServer()))
               ->setId($result->scope)
               ->setDescription($result->description);
    }
}
