<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTirh - Representation of the Tirhuta script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTirh extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Tirh';
    }

    public function number()
    {
        return '326';
    }

    public function unicodeName()
    {
        return 'Tirhuta';
    }
}
