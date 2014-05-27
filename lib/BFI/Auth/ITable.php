<?php

namespace BFI\Auth;

/**
 * Interface ITable
 * @package BFI\Auth
 */
interface ITable
{
    /**
     * The login Method to be implemented
     * @param string $user
     * @param string $hash
     * @return mixed
     */
    public function login($user, $hash);
} 