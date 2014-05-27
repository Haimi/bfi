<?php

namespace BFI\Form\Element;

use BFI\Form\Element;
use BFI\FrontController;

class Wysiwyg extends Text
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'textarea';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        $result = $this->_buildTag('script', array('type' => 'text/javascript'), 'tinymce.init({selector:"#wysiwyg_'.$this->_name.'",language_url:"/js/lang/'.
            FrontController::getInstance()->getPlugin('translate')->getLanguage() .'.js"});');
        $result .= $this->_buildTag($this->type, array('name' => $this->_name, 'id' => 'wysiwyg_' . $this->_name));

        return $result;
    }
}