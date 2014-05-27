<?php

namespace BFI\Form\Element;

use BFI\Form\Element;

class Captcha extends Text
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'text';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addValidator(new \BFI\Form\Validate\Captcha($this->_name));
    }

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        $attribs = array_merge($this->_attributes, array(
            'type' => $this->type,
            'name' => $this->_name,
            'value' => $this->_value
        ));
        $output = '';
        foreach ($this->_errors as $error) {
            // TODO: Translate error messages
            $output .= '<div class="fehler">' . $error . '</div>';
        }
        $seed = rand(1000000000, 9999999999);
        return $this->_buildTag('img', array(
                   'id' => 'captcha_img',
                   'alt' => 'Sicherheitscode',
                   'src' =>  . $seed
               )) .
               $this->_buildTag('img', array(
                   'src' => '/images/icons/refresh.png',
                   'onclick' => "document.getElementById('captcha_img').src = '/static-files/captcha/seed/" . rand(1000000000, 9999999999) . "';";
               )) .
               $this->_buildTag('br') .
               $this->_buildTag('input', $attribs) . $output;
    }
}
