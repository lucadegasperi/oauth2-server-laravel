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
use League\OAuth2\Server\Exception\AccessDeniedException;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;

/**
 * This is the oauth user middleware spec class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class OAuthUserOwnerMiddlewareSpec extends ObjectBehavior
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
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware');
    }

    public function it_passes_if_resource_owners_are_allowed(Request $request, Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('user')->shouldBeCalled();
        $authorizer->setRequest($request)->shouldBeCalled();

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
            ->during('handle', [$request, $this->next]);
    }

    public function it_blocks_if_resource_owners_are_not_allowed(Request $request, Authorizer $authorizer)
    {
        $authorizer->getResourceOwnerType()->willReturn('client')->shouldBeCalled();
        $authorizer->setRequest($request)->shouldBeCalled();

        $this->shouldThrow(new AccessDeniedException())
            ->during('handle', [$request, $this->next]);

        $this->shouldNotThrow(new MiddlewareException('Called execution of $next'))
            ->during('handle', [$request, $this->next]);
    }
}
