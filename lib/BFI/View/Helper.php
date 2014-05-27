<?php

namespace BFI\View;

use BFI\Html\Tag;

abstract class Helper extends Tag
{
    /**
     * The view in which the helper is assigned
     * @var \BFI\View\View
     */
    public $view = null;

    public abstract function render(array $params = array());
}