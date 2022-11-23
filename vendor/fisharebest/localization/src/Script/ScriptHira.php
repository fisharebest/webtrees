<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHira - Representation of the Hiragana script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHira extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hira';
    }

    public function number()
    {
        return '410';
    }

    public function unicodeName()
    {
        return 'Hiragana';
    }
}
