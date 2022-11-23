<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHluw - Representation of the Anatolian Hieroglyphs (Luwian Hieroglyphs, Hittite Hieroglyphs) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHluw extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hluw';
    }

    public function number()
    {
        return '080';
    }

    public function unicodeName()
    {
        return 'Anatolian_Hieroglyphs';
    }
}
