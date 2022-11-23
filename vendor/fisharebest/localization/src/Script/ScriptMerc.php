<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMerc - Representation of the Meroitic Cursive script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMerc extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Merc';
    }

    public function number()
    {
        return '101';
    }

    public function unicodeName()
    {
        return 'Meroitic_Cursive';
    }
}
