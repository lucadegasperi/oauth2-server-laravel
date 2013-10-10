<?php namespace LucaDegasperi\OAuth2Server\Repositories;

interface SessionManagementInterface
{
    public function deleteExpired();
}
