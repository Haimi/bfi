<?php

namespace BFI\Form;

abstract class Filter
{
    /**
     * Filter a value
     * @param string $value
     * @return string
     */
    abstract public function filter($value);
}