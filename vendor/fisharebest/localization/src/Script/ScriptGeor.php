<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGeor - Representation of the Georgian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptGeor extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Geor';
    }

    public function number()
    {
        return '240';
    }

    public function unicodeName()
    {
        return 'Georgian';
    }
}
