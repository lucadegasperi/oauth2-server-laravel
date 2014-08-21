<?php

namespace unit\LucaDegasperi\OAuth2Server\Filters;

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
    }

    function it_filters_against_invalid_access_tokens(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();

        $this->filter('foo', 'bar')->shouldReturn(null);
    }

    function it_filters_against_invalid_scopes(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(false)->shouldBeCalled();

        $this->shouldThrow('\League\OAuth2\Server\Exception\InvalidScopeException')
            ->duringFilter('foo', 'bar', 'baz');
    }

    function it_passes_with_valud_scopes(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(true)->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn(null);
    }

    function it_can_be_set_to_use_http_headers_only_to_check_the_access_token()
    {
        $this->setHttpHeadersOnly(true);
        $this->isHttpHeadersOnly()->shouldReturn(true);

        $this->setHttpHeadersOnly(false);
        $this->isHttpHeadersOnly()->shouldReturn(false);
    }

    public function it_is_possible_to_set_the_scopes_to_check()
    {
        $this->setScopes(['foo', 'bar']);
        $this->getScopes()->shouldReturn(['foo', 'bar']);
    }
}
