<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSarb - Representation of the Old South Arabian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSarb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sarb';
    }

    public function number()
    {
        return '105';
    }

    public function unicodeName()
    {
        return 'Old_South_Arabian';
    }
}
