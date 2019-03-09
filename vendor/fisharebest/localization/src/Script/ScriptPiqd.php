<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPiqd - Representation of the Piqd script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptPiqd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Piqd';
    }

    public function number()
    {
        return '293';
    }
}
