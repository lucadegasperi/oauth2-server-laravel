<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace unit\LucaDegasperi\OAuth2Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use League\OAuth2\Server\Util\RedirectUri;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class AuthorizerSpec extends ObjectBehavior
{
    public function let(AuthorizationServer $issuer, ResourceServer $checker)
    {
        $this->beConstructedWith($issuer, $checker);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Authorizer');
    }

    public function it_issues_an_access_token(AuthorizationServer $issuer)
    {
        $issuer->issueAccessToken()->willReturn('foo')->shouldBeCalled();

        $this->issueAccessToken()->shouldReturn('foo');
    }

    public function it_checks_the_auth_code_request_parameters(AuthorizationServer $issuer, AuthCodeGrant $authCodeGrant)
    {
        $authCodeGrant->checkAuthorizeParams()->willReturn(['foo' => 'bar'])->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();

        $this->checkAuthCodeRequest()->shouldReturn(null);
        $this->getAuthCodeRequestParams()->shouldBe(['foo' => 'bar']);
        $this->getAuthCodeRequestParam('foo')->shouldBe('bar');
    }

    public function it_issues_an_auth_code(AuthorizationServer $issuer, AuthCodeGrant $authCodeGrant)
    {
        $authCodeGrant->newAuthorizeRequest('user', '1', ['foo' => 'bar'])->willReturn('baz')->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();

        $this->issueAuthCode('user', '1', ['foo' => 'bar'])->shouldReturn('baz');
    }

    public function it_returns_the_current_scopes(ResourceServer $checker, AccessTokenEntity $accessTokenEntity)
    {
        $accessTokenEntity->getScopes()->willReturn(['foo', 'bar']);
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->getScopes()->shouldReturn(['foo', 'bar']);
    }

    public function it_checks_if_a_scope_is_included_into_the_current_ones(ResourceServer $checker, AccessTokenEntity $accessTokenEntity)
    {
        $accessTokenEntity->hasScope('foo')->willReturn(true)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->hasScope('foo')->shouldReturn(true);

        $accessTokenEntity->hasScope('foo')->willReturn(false)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->hasScope('foo')->shouldReturn(false);
    }

    public function it_checks_if_multiple_invalid_scopes_are_included_into_the_current_ones(ResourceServer $checker, AccessTokenEntity $accessTokenEntity)
    {
        $accessTokenEntity->hasScope('foo')->willReturn(false)->shouldBecalled();
        $accessTokenEntity->hasScope('bar')->willReturn(false)->shouldNotBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->hasScope(['foo', 'bar'])->shouldReturn(false);
    }

    public function it_checks_if_multiple_mixed_scopes_are_included_into_the_current_ones(ResourceServer $checker, AccessTokenEntity $accessTokenEntity)
    {
        $accessTokenEntity->hasScope('foo')->willReturn(true)->shouldBecalled();
        $accessTokenEntity->hasScope('bar')->willReturn(false)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalledTimes(2);
        $this->hasScope(['foo', 'bar'])->shouldReturn(false);
    }

    public function it_checks_if_multiple_valid_scopes_are_included_into_the_current_ones(ResourceServer $checker, AccessTokenEntity $accessTokenEntity)
    {
        $accessTokenEntity->hasScope('foo')->willReturn(true)->shouldBecalled();
        $accessTokenEntity->hasScope('bar')->willReturn(true)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalledTimes(2);
        $this->hasScope(['foo', 'bar'])->shouldReturn(true);
    }

    public function it_returns_the_resource_owner_id(ResourceServer $checker, AccessTokenEntity $accessTokenEntity, SessionEntity $sessionEntity)
    {
        $sessionEntity->getOwnerId()->willReturn('1')->shouldBeCalled();
        $accessTokenEntity->getSession()->willReturn($sessionEntity)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->getResourceOwnerId()->shouldReturn('1');
    }

    public function it_returns_the_resource_owner_type(ResourceServer $checker, AccessTokenEntity $accessTokenEntity, SessionEntity $sessionEntity)
    {
        $sessionEntity->getOwnerType()->willReturn('user')->shouldBeCalled();
        $accessTokenEntity->getSession()->willReturn($sessionEntity)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->getResourceOwnerType()->shouldReturn('user');
    }

    public function it_returns_the_client_id(ResourceServer $checker, AccessTokenEntity $accessTokenEntity, SessionEntity $sessionEntity, ClientEntity $clientEntity)
    {
        $clientEntity->getId()->willReturn('1')->shouldBeCalled();
        $sessionEntity->getClient()->willReturn($clientEntity)->shouldBeCalled();
        $accessTokenEntity->getSession()->willReturn($sessionEntity)->shouldBeCalled();
        $checker->getAccessToken()->willReturn($accessTokenEntity)->shouldBeCalled();
        $this->getClientId()->shouldReturn('1');
    }

    public function it_returns_the_issuer(AuthorizationServer $issuer)
    {
        $this->getIssuer()->shouldReturn($issuer);
    }

    public function it_returns_the_checker(ResourceServer $checker)
    {
        $this->getChecker()->shouldReturn($checker);
    }

    public function it_sets_the_request_to_the_issuer_and_checker(AuthorizationServer $issuer, ResourceServer $checker, Request $request)
    {
        $issuer->setRequest($request)->shouldBeCalled();
        $checker->setRequest($request)->shouldBeCalled();

        $this->setRequest($request);
    }

    public function it_validates_an_access_token(ResourceServer $checker)
    {
        $checker->isValidRequest(false, null)->shouldBeCalled();

        $this->validateAccessToken(false, null);
    }

    public function it_generates_a_redirect_uri_when_the_user_denies_the_auth_code()
    {
        $this->authCodeRequestDeniedRedirectUri()->shouldReturn('?error=access_denied&error_description=The+resource+owner+or+authorization+server+denied+the+request.');
    }

    public function it_sets_a_redirect_uri_generator(RedirectUri $redirectUri)
    {
        $this->setRedirectUriGenerator($redirectUri);

        $this->getRedirectUriGenerator()->shouldReturn($redirectUri);
    }

    /*function it_sets_a_custom_token_type(AuthorizationServer $issuer, ResourceServer $checker, TokenTypeInterface $tokenType)
    {
        $issuer->setTokenType($tokenType)->shouldBeCalled();
        $checker->setTokenType($tokenType)->shouldBeCalled();

        $this->setTokenType($tokenType);
    }*/
}
