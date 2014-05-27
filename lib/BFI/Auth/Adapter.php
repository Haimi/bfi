<?php

namespace BFI\Auth;

/**
 * Class Adapter
 * @package BFI\Auth
 */
interface Adapter
{
    /**
     * Salt for Passwords (Generated 09/27/2013)
     */
    const PASSWORD_SALT = ')f&BK.)K48CbPmg<-DO+_qq*TJ7k;WXNNB7WWP3xtk:}LOw)+!9D96g84<+~(69~';

    /**
     * Constants for Auth result
     */
    const AUTH_RESULT_SUCCESS = 0;
    const AUTH_RESULT_UNKNOWN_USER = 1;
    const AUTH_RESULT_INVALID_PASSWORD = 2;
    const AUTH_RESULT_USER_INACTIVE = 3;

    /**
     * Authenticate the User
     * @param string $user
     * @param string $password
     * @return int
     */
    public function authenticate($user, $password);
}