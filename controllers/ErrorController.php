<?php

class ErrorController extends \BFI\Controller\AController
{
    public function indexAction()
    {
        if (! is_null($this->_redirect)) {
            $this->_view->e = $this->_redirect->getPrevious();
        }
    }
}