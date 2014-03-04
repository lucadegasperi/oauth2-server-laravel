<?php namespace LucaDegasperi\OAuth2Server\Repositories;

use League\OAuth2\Server\Storage\SessionInterface;
use League\OAuth2\Server\Storage\Adapter;
use League\OAuth2\Server\Entity\Session;
use League\OAuth2\Server\Entity\Scope;
use DB;
use Carbon\Carbon;

class FluentSession extends Adapter implements SessionInterface
{
    /**
     * Get a session from it's identifier
     * @param string $sessionId
     * @return \League\OAuth2\Server\Entity\Session
     */
    public function get($sessionId)
    {
        $result = DB::table('oauth_sessions')
                    ->where('oauth_sessions.id', $sessionId)
                    ->first();

        if(is_null($result)) {
            return null;
        }

        return (new Session($this->getServer()))
                 ->setId($result->id)
                 ->setOwner($result->owner_type, $result->owner_id);
    }

    /**
     * Get a session from an access token
     * @param  string $accessToken The access token
     * @return \League\OAuth2\Server\Entity\Session
     */
    public function getByAccessToken($accessToken)
    {
        // TODO: Implement this method
        $result = DB::table('oauth_sessions')
                ->select('oauth_sessions.*')
                ->join('oauth_access_tokens', 'oauth_session.id', '=', 'oauth_access_tokens.session_id')
                ->where('oauth_access_tokens.token', $accessToken);

        if (is_null($result)) {
            return null;
        }

        return (new Session($this->getServer()))
                 ->setId($result->id)
                 ->setOwner($result->owner_type, $result->owner_id);
    }

    /**
     * Get a session's scopes
     * @param  integer $sessionId
     * @return array Array of \League\OAuth2\Server\Entity\Scope
     */
    public function getScopes($sessionId)
    {
        // TODO: Check this before pushing
        $result = DB::table('oauth_session_scopes')
                        ->select('oauth_scopes.*')
                        ->join('oauth_scopes', 'oauth_session_scopes.scope_id', '=', 'oauth_scopes.id')
                        ->where('oauth_sessions.id', $sessionId)
                        ->get();
        
        $scopes = [];
        
        foreach ($result as $scope) {
            $scopes[] = (new Scope($this->getServer()))
                          ->setId($scope->id)
                          ->setDescription($scope->description);
        }
        
        return $scopes;
    }

    /**
     * Create a new session
     * @param  string $ownerType         Session owner's type (user, client)
     * @param  string $ownerId           Session owner's ID
     * @param  string $clientId          Client ID
     * @param  string $clientRedirectUri Client redirect URI (default = null)
     * @return integer The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        return DB::table('oauth_sessions')->insertGetId([
            'client_id'  => $clientId,
            'owner_type' => $ownerType,
            'owner_id'   => $ownerId,
            'client_redirect_uri' => $clientRedirectUri,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Associate a scope with a session
     * @param  integer $sessionId
     * @param  string  $scopeId    The scopes ID might be an integer or string
     * @return void
     */
    public function associateScope($sessionId, $scopeId)
    {
        DB::table('oauth_session_scopes')->insert([
            'session_id' => $sessionId,
            'scope_id'   => $scopeId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
