<?php
namespace BFI\Plugin;

use BFI\Session;

/**
 * Class User
 * @package BFI\Plugin
 */
class User extends APlugin
{
    public function getCurrentUser()
    {
        $user = Session::get('User');
        if (is_null($user)) {
            $user = new \AQA\User();
        }
        return $user;
    }
}