<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLimb - Representation of the Limbu script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLimb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Limb';
    }

    public function numerals()
    {
        return array('᥆', '᥇', '᥈', '᥉', '᥊', '᥋', '᥌', '᥍', '᥎', '᥏');
    }

    public function number()
    {
        return '336';
    }

    public function unicodeName()
    {
        return 'Limbu';
    }
}
