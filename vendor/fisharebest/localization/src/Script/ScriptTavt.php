<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTavt - Representation of the Tai Viet script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTavt extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Tavt';
    }

    public function number()
    {
        return '359';
    }

    public function unicodeName()
    {
        return 'Tai_Viet';
    }
}
