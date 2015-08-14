<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace unit\LucaDegasperi\OAuth2Server\Middleware;

use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\InvalidRequestException;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;

class CheckAuthCodeRequestMiddlewareSpec extends ObjectBehavior
{
    private $next = null;

    public function __construct()
    {
        $this->next = (function () {
            throw new MiddlewareException('Called execution of $next');
        });
    }

    public function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware');
    }

    public function it_calls_the_next_middleware_on_success(Request $request, Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->shouldBeCalled();
        $authorizer->setRequest($request)->shouldBeCalled();

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
            ->during('handle', [$request, $this->next]);
    }

    public function it_exits_on_error(Request $request, Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->willThrow(new InvalidRequestException('client_id'))->shouldBeCalled();
        $authorizer->setRequest($request)->shouldBeCalled();

        $this->shouldNotThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next]);
    }
}

class MiddlewareException extends \Exception
{
}
