<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHanb - Representation of the Hangul+Bopomofo script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHanb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hanb';
    }

    public function number()
    {
        return '503';
    }
}
