<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLisu - Representation of the Lisu (Fraser) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLisu extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Lisu';
    }

    public function number()
    {
        return '399';
    }

    public function unicodeName()
    {
        return 'Lisu';
    }
}
