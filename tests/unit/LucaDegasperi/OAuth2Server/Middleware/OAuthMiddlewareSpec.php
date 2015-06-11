<?php

namespace unit\LucaDegasperi\OAuth2Server\Middleware;

use Illuminate\Http\Request;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OAuthMiddlewareSpec extends ObjectBehavior
{
    function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware');
    }

    function it_filters_against_invalid_access_tokens(Request $request, Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();

        $next = (function () {
            throw new MiddlewareException('Called execution of $next');
        });

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $next]);

        //$this->filter('foo', 'bar')->shouldReturn(null);
    }

    /*function it_filters_against_invalid_scopes(Authorizer $authorizer)
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
    }*/
}
