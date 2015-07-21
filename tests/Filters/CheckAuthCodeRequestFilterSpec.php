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

class CheckAuthCodeRequestFilterSpec extends ObjectBehavior
{
    public function let(Authorizer $authorizer)
    {
        $this->beConstructedWith($authorizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('LucaDegasperi\OAuth2Server\Filters\CheckAuthCodeRequestFilter');
    }

    public function it_filters_the_auth_code_request_parameters(Authorizer $authorizer)
    {
        $authorizer->checkAuthCodeRequest()->shouldBeCalled();

        $this->filter('foo', 'bar', 'baz')->shouldReturn(null);
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
