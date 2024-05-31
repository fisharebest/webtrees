<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMero - Representation of the Meroitic Hieroglyphs script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMero extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mero';
    }

    public function number()
    {
        return '100';
    }

    public function unicodeName()
    {
        return 'Meroitic_Hieroglyphs';
    }
}
