<?php

namespace spec\LucaDegasperi\OAuth2Server\Filters;

use League\OAuth2\Server\Exception\OAuthException;
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
        $this->shouldImplement('LucaDegasperi\OAuth2Server\Delegates\AuthCodeCheckerDelegate');
    }

    function it_filters_the_auth_code_request_parameters(Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest($this)->willReturn('foo')->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn('foo');
    }

    function it_responds_null_when_the_auth_code_request_parameters_are_valid()
    {
        $this->checkSuccessful()->shouldReturn(null);
    }

    function it_returns_a_json_response_when_the_auth_code_request_parameters_are_invalid(OAuthException $e)
    {
        $e->getHttpHeaders()->willReturn([])->shouldBeCalled();
        $this->checkFailed($e)->shouldReturnAnInstanceOf('Illuminate\Http\JsonResponse');
        $this->checkFailed($e)->getData()->shouldHaveKey('error');
        $this->checkFailed($e)->getData()->shouldHaveKey('error_message');
        $this->checkFailed($e)->getStatusCode()->shouldBe(400);
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
