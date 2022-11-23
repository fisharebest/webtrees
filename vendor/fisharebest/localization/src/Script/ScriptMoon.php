<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMoon - Representation of the Moon (Moon code, Moon script, Moon type) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMoon extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Moon';
    }

    public function number()
    {
        return '218';
    }
}
