<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhli - Representation of the Inscriptional Pahlavi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPhli extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Phli';
    }

    public function number()
    {
        return '131';
    }

    public function unicodeName()
    {
        return 'Inscriptional_Pahlavi';
    }
}
