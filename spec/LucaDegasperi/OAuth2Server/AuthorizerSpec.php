<?php

namespace spec\LucaDegasperi\OAuth2Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Grant\AuthCode;
use League\OAuth2\Server\ResourceServer;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenIssuerDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AccessTokenValidatorDelegate;
use LucaDegasperi\OAuth2Server\Delegates\AuthCodeCheckerDelegate;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function it_issues_an_access_token(AccessTokenIssuerDelegate $delegate, AuthorizationServer $issuer)
    {
        $issuer->issueAccessToken()->willReturn('foo')->shouldBeCalled();
        $delegate->accessTokenIssued('foo')->willReturn('bar')->shouldBeCalled();

        $this->issueAccessToken($delegate)->shouldReturn('bar');
    }

    function it_catches_an_exception_when_issuing_an_access_token_fails(AccessTokenIssuerDelegate $delegate, AuthorizationServer $issuer)
    {
        $exception = new OAuthException();
        $issuer->issueAccessToken()->willThrow($exception);
        $delegate->accessTokenIssuingFailed($exception)->willReturn('baz')->shouldBeCalled();

        $this->issueAccessToken($delegate)->shouldReturn('baz');
    }

    function it_checks_the_auth_code_request_parameters(AuthCodeCheckerDelegate $delegate, AuthorizationServer $issuer, AuthCode $authCodeGrant)
    {
        $authCodeGrant->checkAuthoriseParams()->willReturn(['foo' => 'bar'])->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();
        $delegate->checkSuccessful()->willReturn('baz')->shouldBeCalled();

        $this->checkAuthCodeRequest($delegate)->shouldReturn('baz');
        $this->getAuthCodeRequestParams()->shouldBe(['foo' => 'bar']);
    }

    function it_catches_an_exception_with_invalid_auth_code_request_parameters(AuthCodeCheckerDelegate $delegate, AuthorizationServer $issuer, AuthCode $authCodeGrant)
    {
        $exception = new OAuthException();
        $authCodeGrant->checkAuthoriseParams()->willThrow($exception);
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();
        $delegate->checkFailed($exception)->willReturn('baz')->shouldBeCalled();

        $this->checkAuthCodeRequest($delegate)->shouldReturn('baz');
        $this->getAuthCodeRequestParams()->shouldBe([]);
    }

    function it_issues_an_auth_code(AuthorizationServer $issuer, AuthCode $authCodeGrant)
    {
        $authCodeGrant->newAuthoriseRequest('user', '1', ['foo' => 'bar'])->willReturn('baz')->shouldBeCalled();
        $issuer->getGrantType('authorization_code')->willReturn($authCodeGrant)->shouldBeCalled();

        $this->issueAuthCode('user', '1', ['foo' => 'bar'])->shouldReturn('baz');
    }

    function it_delegates_when_the_access_token_validation_succeeds(AccessTokenValidatorDelegate $delegate, ResourceServer $checker)
    {
        $checker->isValidRequest(false, null)->willReturn(true)->shouldBeCalled();
        $delegate->accessTokenValidated()->willReturn('foo')->shouldBeCalled();

        $this->validateAccessToken($delegate)->shouldReturn('foo');
    }

    function it_delegates_when_the_access_token_validation_fails(AccessTokenValidatorDelegate $delegate, ResourceServer $checker)
    {
        $checker->isValidRequest(false, null)->willReturn(false)->shouldBeCalled();
        $delegate->accessTokenValidationFailed()->willReturn('foo')->shouldBeCalled();

        $this->validateAccessToken($delegate)->shouldReturn('foo');
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
}
