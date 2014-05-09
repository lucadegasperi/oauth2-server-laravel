<?php

namespace unit\LucaDegasperi\OAuth2Server\Filters;

use League\OAuth2\Server\Exception\AccessDeniedException;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OAuthFilterSpec extends ObjectBehavior
{
    function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\OAuthFilter');
        $this->shouldImplement('LucaDegasperi\OAuth2Server\Delegates\AccessTokenValidatorDelegate');
    }

    function it_filters_against_invalid_access_tokens(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken($this, false)->willReturn('foo')->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn('foo');
    }

    function it_returns_null_when_no_scopes_have_to_be_validated(Authorizer $authorizer)
    {
        $this->accessTokenValidated()->shouldReturn(null);
    }

    function it_returns_null_when_scopes_are_valid(Authorizer $authorizer)
    {
        $authorizer->hasScope(['foo'])->willReturn(true)->shouldBeCalled();
        $this->setScopes(['foo']);
        $this->accessTokenValidated()->shouldReturn(null);
    }

    function it_returns_a_json_response_when_scopes_are_invalid(Authorizer $authorizer)
    {
        $authorizer->hasScope(['bar'])->willReturn(false)->shouldBeCalled();
        $this->setScopes(['bar']);
        $this->accessTokenValidated()->shouldReturnAnInstanceOf('Illuminate\Http\JsonResponse');
        $this->accessTokenValidated()->getData()->shouldHaveKey('error');
        $this->accessTokenValidated()->getData()->shouldHaveKey('error_message');
        $this->accessTokenValidated()->getStatusCode()->shouldBe(403);
    }

    function it_returns_a_json_response_when_access_token_validation_fails(Authorizer $authorizer)
    {
        $exception = new AccessDeniedException();
        $this->accessTokenValidationFailed($exception)->shouldReturnAnInstanceOf('Illuminate\Http\JsonResponse');
        $this->accessTokenValidationFailed($exception)->getData()->shouldHaveKey('error');
        $this->accessTokenValidationFailed($exception)->getData()->shouldHaveKey('error_message');
        $this->accessTokenValidationFailed($exception)->getStatusCode()->shouldBe(401);
    }

    function it_can_be_set_to_use_http_headers_only_to_check_the_access_token()
    {
        $this->setHttpHeadersOnly(true);
        $this->isHttpHeadersOnly()->shouldReturn(true);

        $this->setHttpHeadersOnly(false);
        $this->isHttpHeadersOnly()->shouldReturn(false);
    }

    public function getMatchers()
    {
        return [
            'haveKey' => function($subject, $key) {
                    return array_key_exists($key, $subject);
                },
        ];
    }
}
