<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Cypro-Minoan script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptCpmn extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Cpmn';
    }

    public function number()
    {
        return '402';
    }

    public function unicodeName()
    {
        return 'Cypro_Minoan';
    }
}
