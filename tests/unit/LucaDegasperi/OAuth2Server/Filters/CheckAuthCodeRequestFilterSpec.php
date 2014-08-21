<?php

namespace unit\LucaDegasperi\OAuth2Server\Filters;

use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CheckAuthCodeRequestFilterSpec extends ObjectBehavior
{
    function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter');
    }

    function it_filters_the_auth_code_request_parameters(Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn(null);
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
