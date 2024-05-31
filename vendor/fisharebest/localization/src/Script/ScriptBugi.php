<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBugi - Representation of the Buginese script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptBugi extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Bugi';
    }

    public function number()
    {
        return '367';
    }

    public function unicodeName()
    {
        return 'Buginese';
    }
}
