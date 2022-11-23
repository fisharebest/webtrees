<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArab - Representation of the Arabic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptArab extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Arab';
    }

    public function numerals()
    {
        return array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    }

    public function number()
    {
        return '160';
    }

    public function unicodeName()
    {
        return 'Arabic';
    }
}
