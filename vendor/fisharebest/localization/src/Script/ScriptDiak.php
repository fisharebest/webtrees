<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptDiak - Representation of the Diak script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptDiak extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Diak';
    }

    public function number()
    {
        return '342';
    }

    public function unicodeName()
    {
        return 'Dives_Akuru';
    }
}
