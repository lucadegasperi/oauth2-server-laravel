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
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * Create a new abstract fluent adapter instance.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->connectionName = null;
    }

    /**
     * Set the resolver.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $resolver
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get the resolver.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Set the connection name.
     *
     * @param string $connectionName
     *
     * @return void
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    /**
     * Get the connection.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function getConnection()
    {
        return $this->resolver->connection($this->connectionName);
    }
}
