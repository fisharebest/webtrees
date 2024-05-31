<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLatf - Representation of the Latin (Fraktur variant) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLatf extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Latf';
    }

    public function number()
    {
        return '217';
    }
}
