<?php
/**
 * Created by JetBrains PhpStorm.
 * User: haimi
 * Date: 26.09.13
 * Time: 11:22
 * To change this template use File | Settings | File Templates.
 */

namespace BFI;


use BFI\View\Helper\Uri;

class Facebook
{
    protected $_connection = null;

    protected $_user = null;

    public function __construct()
    {
        $this->_connection = new \Facebook\Facebook(array(
            'appId'  => Config::get('FACEBOOK_APP_ID'),
            'secret' => Config::get('FACEBOOK_APP_SECRET'),
        ));
        $this->_user = $this->_connection->getUser();
    }

    public function isAuthenticated()
    {
        return ($this->_user > 0);
    }

    public function register()
    {
        $uri = new Uri();
        return $this->_connection->getLoginUrl(array(
            'scope' => 'read_stream, publish_actions, xmpp_login',
            'redirect_uri' => $uri->getAbsoluteUri(array('controller' => 'register', 'action' => 'facebook'))
        ));
    }

    public function login()
    {
        $uri = new Uri();
        return $this->_connection->getLoginUrl(array(
            'scope' => 'read_stream, publish_actions, xmpp_login',
            'redirect_uri' => $uri->getAbsoluteUri(array('controller' => 'login', 'action' => 'facebook'))
        ));
    }

    public function logout()
    {
        return $this->_connection->getLogoutUrl();
    }
}