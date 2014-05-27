<?php

namespace BFI\Plugin;


use BFI\Plugin\APlugin;
use BFI\Session;

final class Image extends APlugin
{

    /**
     * @var array
     */
    protected $_translateValues = array();

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
                    $this->_translateValues[strval($trans['tuid'])] = array(
                        strval($tVal->path),
                        strval($tVal->alt)
                    );
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
            return sprintf('<img src="/images/%s" alt="%s" title="%s" />',
                $this->_translateValues[$key][0],
                $this->_translateValues[$key][1],
                $this->_translateValues[$key][1]
            );
        }
        if (DEBUG) {
            return '%%image:' . $key . '%%' ;
        }
        return $key;
    }
}