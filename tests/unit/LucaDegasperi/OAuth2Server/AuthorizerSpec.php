<?php

namespace unit\LucaDegasperi\OAuth2Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use League\OAuth2\Server\Util\RedirectUri;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class AuthorizerSpec extends ObjectBehavior
{
    function let(AuthorizationServer $issuer, ResourceServer $checker)
    {
        $this->beConstructedWith($issuer, $checker);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Authorizer');
    }

    function it_issues_an_access_token(AuthorizationServer $issuer)
    {
        $issuer->issueAccessToken([])->willReturn('foo')->shouldBeCalled();

        $this->issueAccessToken([])->shouldReturn('foo');
    }

    function it_checks_the_auth_code_request_parameters(AuthorizationServer $issuer, AuthCodeGrant $authCodeGrant)
    {
        $authCodeGrant->checkAuthorizeParams()->willReturn(['foo' => 'bar'])->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();

        $this->checkAuthCodeRequest()->shouldReturn(null);
        $this->getAuthCodeRequestParams()->shouldBe(['foo' => 'bar']);
        $this->getAuthCodeRequestParam('foo')->shouldBe('bar');
    }

    function it_issues_an_auth_code(AuthorizationServer $issuer, AuthCodeGrant $authCodeGrant)
    {
        $authCodeGrant->newAuthorizeRequest('user', '1', ['foo' => 'bar'])->willReturn('baz')->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();

        $this->issueAuthCode('user', '1', ['foo' => 'bar'])->shouldReturn('baz');
    }

    function it_returns_the_current_scopes(ResourceServer $checker)
    {
        $checker->getScopes()->willReturn(['foo', 'bar'])->shouldBeCalled();
        $this->getScopes()->shouldReturn(['foo', 'bar']);
    }

    function it_checks_if_a_scope_is_included_into_the_current_ones(ResourceServer $checker)
    {
        $checker->hasScope('foo')->willReturn(true)->shouldBeCalled();
        $this->hasScope('foo')->shouldReturn(true);

        $checker->hasScope(['foo', 'bar'])->willReturn(false)->shouldBeCalled();
        $this->hasScope(['foo', 'bar'])->shouldReturn(false);
    }

    function it_returns_the_resource_owner_id(ResourceServer $checker)
    {
        $checker->getOwnerId()->willReturn('1')->shouldBeCalled();
        $this->getResourceOwnerId()->shouldReturn('1');
    }

    function it_returns_the_resource_owner_type(ResourceServer $checker)
    {
        $checker->getOwnerType()->willReturn('user')->shouldBeCalled();
        $this->getResourceOwnerType()->shouldReturn('user');
    }

    function it_returns_the_client_id(ResourceServer $checker)
    {
        $checker->getClientId()->willReturn('1')->shouldBeCalled();
        $this->getClientId()->shouldReturn('1');
    }

    function it_returns_the_issuer(AuthorizationServer $issuer)
    {
        $this->getIssuer()->shouldReturn($issuer);
    }

    function it_returns_the_checker(ResourceServer $checker)
    {
        $this->getChecker()->shouldReturn($checker);
    }

    function it_sets_the_request_to_the_issuer_and_checker(AuthorizationServer $issuer, ResourceServer $checker, Request $request)
    {
        $issuer->setRequest($request)->shouldBeCalled();
        $checker->setRequest($request)->shouldBeCalled();

        $this->setRequest($request);
    }

    function it_validates_an_access_token(ResourceServer $checker)
    {
        $checker->isValidRequest(false, null)->shouldBeCalled();

        $this->validateAccessToken(false, null);
    }

    function it_generates_a_redirect_uri_when_the_user_denies_the_auth_code()
    {
        $this->authCodeRequestDeniedRedirectUri()->shouldReturn('?error=access_denied&error_description=The+resource+owner+or+authorization+server+denied+the+request.');
    }

    function it_sets_a_redirect_uri_generator(RedirectUri $redirectUri)
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
