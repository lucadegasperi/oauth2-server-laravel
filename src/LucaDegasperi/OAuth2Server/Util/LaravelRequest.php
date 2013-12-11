<?php namespace LucaDegasperi\OAuth2Server\Util;

use League\OAuth2\Server\Util\Request;
use Cookie;
use Input;

class LaravelRequest extends Request {

    protected $put = array();
    protected $delete = array();

    public function __construct()
    {
        $input = Input::all();
        $files = Input::file();
        $cookie = Input::cookie();

        parent::__construct($input, $input, $cookie, $files, $_SERVER);

        $this->put = $input;
        $this->delete = $input;
    }

    public function put($index = null, $default = null)
    {
        return $this->getPropertyValue('put', $index, $default);
    }

    public function delete($index = null, $default = null)
    {
        return $this->getPropertyValue('delete', $index, $default);
    }
}