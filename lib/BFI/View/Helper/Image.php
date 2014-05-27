<?php

namespace BFI\View\Helper;

use BFI\FrontController;
use BFI\View\Helper;

class Image extends Helper
{
    const IMG_PATH_PREFIX = '/images/';

    /**
     * @param array $params
     * @return \BFI\Plugin\Image
     */
    public function render(array $params = array())
    {
        $params = $params[0];
        if (! is_null(FrontController::getInstance()->getPlugin('image'))) {
            return FrontController::getInstance()->getPlugin('image')->_($params);
        }
        if (! array_key_exists('src', $params)) {
            return '';
        } else {
            $params['src'] = self::IMG_PATH_PREFIX . $params['src'];
        }
        if (! array_key_exists('alt', $params)) {
            if (array_key_exists('title', $params)) {
                $params['alt'] = $params['title'];
            } else {
                $params['alt'] = 'MISSING IMAGE DESCRIPTION';
                $params['title'] = 'MISSING IMAGE DESCRIPTION';
            }
        }
        if (! array_key_exists('title', $params)) {
            $params['title'] = $params['alt'];
        }
        return $this->_buildTag('img', $params);
    }
}