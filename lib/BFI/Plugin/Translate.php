<?php

namespace BFI\Plugin;

use BFI\Plugin\APlugin;
use BFI\Session;

/**
 * Class Translate
 * @package BFI\Plugin
 */
class Translate extends APlugin
{
    /**
     * @var array
     */
    protected $_translateValues = array();

    /**
     * @var string
     */
    protected $_language = 'de';

    /**
     * C'tor
     * @param string $file
     * @param string $lang
     */
    public function __construct($file, $lang = null)
    {
        if (is_null($lang)) {
            $lang = Session::get('lang');
        }
        $xml = simplexml_load_file(BASE_PATH . '/config/' . $file);
        foreach ($xml->translation as $trans) {
            foreach ($trans->value as $tVal) {
                if (strval($tVal['lang']) === $lang) {
                    $this->_translateValues[strval($trans['tuid'])] = strval($tVal);
                }
            }
        }
    }

    /**
     * Return a translation
     * @param string $key
     * @return string
     */
    public function _($key)
    {
        if (is_null($key)) {
            return '';
        }
        if (array_key_exists($key, $this->_translateValues)) {
            return $this->_translateValues[$key];
        }
        if (DEBUG) {
            return '%%' . $key . '%%' ;
        }
        return $key;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }


}