<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMult - Representation of the  Multani script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMult extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mult';
    }

    public function number()
    {
        return '323';
    }

    public function unicodeName()
    {
        return 'Multani';
    }
}
