<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZmth - Representation of the Mathematical notation script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptZmth extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Zmth';
    }

    public function number()
    {
        return '995';
    }
}
