<?php

namespace BFI\View\Helper;

use BFI\FrontController;
use BFI\View\Helper;

class Message extends Helper
{
    public function render(array $params = array())
    {
        $failure = FrontController::getInstance()->getRouter()->getParam('meldung');
        if (! is_null($failure)) {
            echo '<div id="fehlerfenster">'.urldecode($failure).'</div>';
        }
    }
}