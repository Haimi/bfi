<?php
/**
 * Created by PhpStorm.
 * User: sascha
 * Date: 16.11.13
 * Time: 20:52
 */

namespace BFI;


class Format
{
    public static function toEntities($str)
    {
        if (is_array($str)) {
            return self::maskName($str[0]);
        } else {
            return self::maskName($str);
        }
    }

    private function maskName($str)
    {
        $result = '';
        for ($i = 0; $i < mb_strlen($str); $i++) {
            $result .= '&#' . ord(mb_substr($str, $i, 1)) . ';';
        }
        return $result;
    }


} 