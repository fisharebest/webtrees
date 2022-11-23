<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNarb - Representation of the Old North Arabian (Ancient North Arabian) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptNarb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Narb';
    }

    public function number()
    {
        return '106';
    }

    public function unicodeName()
    {
        return 'Old_North_Arabian';
    }
}
