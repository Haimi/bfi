<?php

namespace BFI\View\Helper;

use BFI\FrontController;
use BFI\Plugin\Translate;
use BFI\View\Helper;

class Icon extends Helper
{
    const ICON_MAIN_DOME = 0;

    /**
     * @var Translate
     */
    protected $_trans = null;

    /**
     * C'tor
     */
    public function __construct()
    {
        $this->_trans = FrontController::getInstance()->getPlugin('translate');
    }

    public function render(array $params = array())
    {
        $icon = '';
        switch ($params[0]) {
            case self::ICON_MAIN_DOME:
                if ($params[1] === 1) {
                    $icon = ' ' . $this->_buildTag('img', array(
                        'src' => '/images/icons/hq.png',
                        'alt' => $this->_('Hauptkuppel'),
                        'title' => $this->_('Hauptkuppel'),
                        'class' => 'infoicon'
                    ));
                }
                break;
        }
        return $icon;
    }

    /**
     * Translator mapping
     * @param string $key
     * @return string
     */
    public function _($key)
    {
        return $this->_trans->_($key);
    }
}