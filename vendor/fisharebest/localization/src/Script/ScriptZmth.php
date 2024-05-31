<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZmth - Representation of the Mathematical notation script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
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
