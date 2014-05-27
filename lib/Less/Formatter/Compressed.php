<?php
/**
 * Created by JetBrains PhpStorm.
 * User: haimi
 * Date: 25.09.13
 * Time: 18:27
 * To change this template use File | Settings | File Templates.
 */

namespace Less\Formatter;

class Compressed extends Classic {
    public $disableSingle = true;
    public $open = "{";
    public $selectorSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";
    public $compressColors = true;

    public function indentStr($n = 0) {
        return "";
    }
}