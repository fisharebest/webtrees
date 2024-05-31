<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMand - Representation of the Mandaic, Mandaean script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMand extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mand';
    }

    public function number()
    {
        return '140';
    }

    public function unicodeName()
    {
        return 'Mandaic';
    }
}
