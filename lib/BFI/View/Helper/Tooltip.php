<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

class Tooltip extends Helper
{
    public function render(array $params = array())
    {
        if ($params[0] === 'building') {
            $conf = $params[1]; // First param: 'building', second param Building Array
            return $this->_getWrapper($conf['position'],
                $this->view->_('name_gebaeude_' . $conf['gebaeude']) . ' ' .
                $this->_buildTag('span', array('class' => 'weiss'), '-') . ' ' .
                $conf['stufe']
            );
        }
        if ($params[0] === 'emptySpot') {
            $conf = $params[1]; // First param: 'building', second param Building Array
            return $this->_getWrapper('building_' . $conf['position'],
                $this->view->_('text_gebneu_short')
            );
        }
        return '';
        //<div id="XXXX_XX" class="maustip_auto"><b class="blau">CONTENT</b></div>
    }

    /**
     * Get Tooltip Wrapper
     * @param string $content
     * @return string
     */
    protected function _getWrapper($id, $content)
    {
        return $this->_buildTag('div', array(
                'id'    => 'ttbuilding_' . $id,
                'class' => 'maustip_auto'
            ),
            $this->_buildTag('b', array(
                'class' => 'blau'
            ), $content)
        );
    }
}