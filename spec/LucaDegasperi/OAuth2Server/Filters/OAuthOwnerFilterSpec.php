<?php

namespace spec\LucaDegasperi\OAuth2Server\Filters;

use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OAuthOwnerFilterSpec extends ObjectBehavior
{
    function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
    }

    function it_passes_if_resource_owners_are_allowed(Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('user')->shouldBeCalled();

        $this->filter('foo', 'bar', 'user')->shouldReturn(null);
    }

    function it_filters_if_resource_owners_are_not_allowed(Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('user')->shouldBeCalled();
        $this->filter('foo', 'bar', 'client')->shouldReturnAnInstanceOf('Illuminate\Http\JsonResponse');
        $this->filter('foo', 'bar', 'client')->getData()->shouldHaveKey('error');
        $this->filter('foo', 'bar', 'client')->getData()->shouldHaveKey('error_message');
        $this->filter('foo', 'bar', 'client')->getStatusCode()->shouldBe(403);
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
