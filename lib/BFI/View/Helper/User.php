<?php

namespace BFI\View\Helper;

use BFI\FrontController;
use BFI\View\Helper;

class User extends Helper
{
    /**
     * @param array $params
     * @return \AQA\User
     */
    public function render(array $params = array())
    {
        return FrontController::getInstance()->getPlugin('user')->getCurrentUser();
    }
}