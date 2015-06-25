<?php

namespace unit\LucaDegasperi\OAuth2Server\Middleware;

use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\InvalidScopeException;
use LucaDegasperi\OAuth2Server\Authorizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OAuthMiddlewareSpec extends ObjectBehavior
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
        $this->beConstructedWith($authorizer, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware');
    }

    function it_blocks_invalid_access_tokens(Request $request, Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->willThrow(new AccessDeniedException())->shouldBeCalled();

        $this->shouldNotThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next]);
    }

    function it_passes_with_valid_access_token(Request $request, Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->shouldBeCalled();

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next]);
    }

    function it_block_invalid_scopes(Request $request, Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(false)->shouldBeCalled();

        $this->shouldThrow(new InvalidScopeException('baz'))
                ->during('handle', [$request, $this->next, 'baz']);

        $this->shouldNotThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next, 'baz']);
    }

    function it_passes_with_valid_scopes(Request $request, Authorizer $authorizer)
    {
        $authorizer->validateAccessToken(false)->shouldBeCalled();
        $authorizer->hasScope(['baz'])->willReturn(true)->shouldBeCalled();

        $this->shouldNotThrow(new InvalidScopeException('baz'))
                ->during('handle', [$request, $this->next, 'baz']);

        $this->shouldThrow(new MiddlewareException('Called execution of $next'))
                ->during('handle', [$request, $this->next, 'baz']);
    }
}
