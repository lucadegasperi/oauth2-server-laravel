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

class OAuthOwnerFilterSpec extends ObjectBehavior
{
    public function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
    }

    public function it_passes_if_resource_owners_are_allowed(Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('user')->shouldBeCalled();

        $this->filter('foo', 'bar', 'user')->shouldReturn(null);
    }

    public function it_filters_if_resource_owners_are_not_allowed(Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('user')->shouldBeCalled();
        $this->shouldThrow('\League\OAuth2\Server\Exception\AccessDeniedException')->duringFilter('foo', 'bar', 'client');
    }

    public function getMatchers()
    {
        return [
            'haveKey' => function ($subject, $key) {
                    return array_key_exists($key, $subject);
                },
        ];
    }
}
