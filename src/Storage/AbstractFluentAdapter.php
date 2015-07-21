<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use League\OAuth2\Server\Storage\AbstractStorage;

/**
 * This is the abstract fluent adapter class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
abstract class AbstractFluentAdapter extends AbstractStorage
{
    /**
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * @var string
     */
    protected $connectionName;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->connectionName = null;
    }

    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver()
    {
        return $this->resolver;
    }

    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    protected function getConnection()
    {
        return $this->resolver->connection($this->connectionName);
    }
}
