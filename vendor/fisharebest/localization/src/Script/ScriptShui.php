<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Shuishu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptShui extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Shui';
    }

    public function number()
    {
        return '530';
    }
}
