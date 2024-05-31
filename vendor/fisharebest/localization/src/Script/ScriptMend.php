<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMend - Representation of the Mende Kikakui script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMend extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mend';
    }

    public function number()
    {
        return '438';
    }

    public function unicodeName()
    {
        return 'Mende_Kikakui';
    }
}
