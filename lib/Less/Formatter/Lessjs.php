<?php
/**
 * Created by JetBrains PhpStorm.
 * User: haimi
 * Date: 25.09.13
 * Time: 18:28
 * To change this template use File | Settings | File Templates.
 */

namespace Less\Formatter;


class Lessjs extends Classic {
    public $disableSingle = true;
    public $breakSelectors = true;
    public $assignSeparator = ": ";
    public $selectorSeparator = ",";
}
