<?php

namespace BFI\Form;
use BFI\Html\Tag;

/**
 * Class Decorator
 * @package BFI\Form
 */
abstract class Decorator extends Tag
{
    /**
     * Render the Element
     * @return string
     */
    abstract public function render();
} 