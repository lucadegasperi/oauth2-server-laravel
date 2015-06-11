<?php

namespace unit\LucaDegasperi\OAuth2Server\Middleware;

use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\InvalidRequestException;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CheckAuthCodeRequestMiddlewareSpec extends ObjectBehavior
{
    private $next = null;

    public function __construct()
    {
        $this->next = (function () {
            throw new MiddlewareException('Called execution of $next');
        });
    }

    function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware');
    }

    function it_calls_the_next_middleware_on_success(Request $request, Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->shouldBeCalled();

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
            ->during('handle', [$request, $this->next]);
    }

    function it_exits_on_error(Request $request, Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->willThrow(new InvalidRequestException('client_id'))->shouldBeCalled();

        $this->shouldNotThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next]);
    }
}

class MiddlewareException extends \Exception
{

}
