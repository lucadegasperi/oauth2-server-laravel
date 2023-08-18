<?php namespace Tikamsah\OAuth2Server\Repositories;

interface SessionManagementInterface
{
    public function deleteExpired();
}
