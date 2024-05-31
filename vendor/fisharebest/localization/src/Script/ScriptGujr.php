<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGujr - Representation of the Gujarati script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptGujr extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Gujr';
    }

    public function numerals()
    {
        return array('૦', '૧', '૨', '૩', '૪', '૫', '૬', '૭', '૮', '૯');
    }

    public function number()
    {
        return '320';
    }

    public function unicodeName()
    {
        return 'Gujarati';
    }
}
