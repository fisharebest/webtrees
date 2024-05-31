<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKana - Representation of the Katakana script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKana extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Kana';
    }

    public function number()
    {
        return '411';
    }

    public function unicodeName()
    {
        return 'Katakana';
    }
}
