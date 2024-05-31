<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAran - Representation of the Arabic (Nastaliq) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptAran extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Aran';
    }

    public function number()
    {
        return '161';
    }
}
