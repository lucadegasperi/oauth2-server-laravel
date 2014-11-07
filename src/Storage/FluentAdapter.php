<?php
/**
 * Fluent adapter for an OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use League\OAuth2\Server\Storage\AbstractStorage;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

abstract class FluentAdapter extends AbstractStorage
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
