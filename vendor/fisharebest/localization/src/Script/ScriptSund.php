<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSund - Representation of the Sundanese script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSund extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sund';
    }

    public function numerals()
    {
        return array('᮰', '᮱', '᮲', '᮳', '᮴', '᮵', '᮶', '᮷', '᮸', '᮹');
    }

    public function number()
    {
        return '362';
    }

    public function unicodeName()
    {
        return 'Sundanese';
    }
}
