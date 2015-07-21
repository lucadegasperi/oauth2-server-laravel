<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace unit\LucaDegasperi\OAuth2Server\Filters;

use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;

class OAuthFilterSpec extends ObjectBehavior
{
    public function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer, false);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\OAuthFilter');
    }

    public function it_filters_against_invalid_access_tokens(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();

        $this->filter('foo', 'bar')->shouldReturn(null);
    }

    public function it_filters_against_invalid_scopes(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(false)->shouldBeCalled();

        $this->shouldThrow('\League\OAuth2\Server\Exception\InvalidScopeException')
            ->duringFilter('foo', 'bar', 'baz');
    }

    public function it_passes_with_valud_scopes(Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willReturn('foo')->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(true)->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn(null);
    }

    public function it_can_be_set_to_use_http_headers_only_to_check_the_access_token()
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
