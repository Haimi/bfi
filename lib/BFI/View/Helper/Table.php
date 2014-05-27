<?php

namespace BFI\View\Helper;

use BFI\FrontController;
use BFI\Plugin\Translate;
use BFI\View\Helper;

/**
 * Class Table
 * @package BFI\View\Helper
 */
class Table extends Helper
{
    /**
     * Render Data to a table
     * @param array $params
     * @usage $this->table(array $data, string $id)
     * @return string
     */
    public function render(array $params = array())
    {
        if (count($params[0]) < 1) {
            return '';
        }
        $columns = array_keys($params[0][0]);
        // Build head
        $headRow = '';
        /** @var Translate $translate */
        $translate = FrontController::getInstance()->getPlugin('translate');
        foreach ($columns as $col) {
            $headRow .= $this->_buildTag('th', array(), $translate->_('col_name_' . $col));
        }
        $head = $this->_buildTag('thead', array(), $this->_buildTag('tr', array(), $headRow));
        // Build Body
        $body = '';
        foreach ($params[0] as $row) {
            $rowContent = '';
            foreach ($row as $colVal) {
                $rowContent .= $this->_buildTag('td', array(), $colVal);
            }
            $body .= $this->_buildTag('tr', array(), $rowContent);
        }
        $body = $this->_buildTag('tbody', array(), $body);
        $options = array();
        if (array_key_exists(1, $params)) {
            $options['id'] = $params[1];
        }
        return $this->_buildTag('table', $options, $head . $body);
    }
}