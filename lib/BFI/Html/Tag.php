<?php

namespace BFI\Html;


class Tag {

    /**
     * Array of all html singleton tags
     * @var array
     */
    private $_singletonTags = array(
        'area',
        'base',
        'basefont',
        'br',
        'col',
        'frame',
        'hr',
        'img',
        'input',
        'isindex',
        'link',
        'meta',
        'param'
    );

    /**
     * Build a HTML Tag with content
     * @param string $name
     * @param array $attributes
     * @param string $content
     * @return string
     */
    protected function _buildTag($name, array $attributes = array(), $content = '')
    {
        $attrParts = array();
        foreach ($attributes as $attrKey => $attrVal) {
            $attrParts[] = $attrKey . '="' . str_replace('"','&quot;', $attrVal) . '"';
        }
        if (in_array($name, $this->_singletonTags)) {
            return sprintf('<%s %s />', $name, implode(' ', $attrParts));
        }
        return sprintf('<%s %s>%s</%s>', $name, implode(' ', $attrParts), $content, $name);
    }
}