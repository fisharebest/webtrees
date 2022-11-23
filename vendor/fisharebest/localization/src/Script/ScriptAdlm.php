<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAdlm - Representation of the Adlam script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptAdlm extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Adlm';
    }

    public function number()
    {
        return '166';
    }

    public function unicodeName()
    {
        return 'Adlam';
    }
}
