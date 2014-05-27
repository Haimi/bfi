<?php
/**
 * Created by PhpStorm.
 * User: haimi
 * Date: 10.01.14
 * Time: 13:15
 */

namespace BFI\Form\Decorator;


use BFI\FrontController;

/**
 * Class Wysiwyg
 * @package BFI\Form\Decorator
 */
class Wysiwyg extends Element
{
    /**
     * Render the Wysiwyg Element
     * @return string
     */
    public function render()
    {
        $script = $this->_buildTag(
            'script',
            array('type' => 'text/javascript'),
            'tinymce.init({selector:"#wysiwyg_'.$this->_element->getName() . '",language_url:"/js/lang/'.
            FrontController::getInstance()->getPlugin('translate')->getLanguage() .'.js"});'
        );
        $attribs = array_merge($this->_element->getAttributes(), array(
            'name' => $this->_element->getName(),
            'id' => 'wysiwyg_' . $this->_element->getName()
        ));
        return $script . $this->_buildTag($this->_element->type, $attribs, $this->_element->getValue());
    }
} 