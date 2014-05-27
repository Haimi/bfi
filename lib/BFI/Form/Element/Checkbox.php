<?php

namespace BFI\Form\Element;

use BFI\Form\Element;

class Checkbox extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'checkbox';

    /**
     * Set checkbox checked
     * @var bool
     */
    protected $_checked = false;

    /**
     * Set checkbox checked
     * @param bool $checked
     * @return Checkbox
     */
    public function setChecked($checked = true)
    {
        $this->_checked = (bool) $checked;
        return $this;
    }

    /**
     * Get if Checkbox is Checked
     * @return bool
     */
    public function isChecked()
    {
        return $this->_checked;
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
            'value' => $this->_value,
        ));
        if($this->_checked == true) {
            $attribs['checked'] = 'checked';
        }
        $output = '';
        foreach ($this->_errors as $error) {
            // TODO: Translate error messages
            $output .= '<div class="fehler">' . $error . '</div>';
        }
        return $this->_buildTag('input', $attribs) . $output;
    }
}