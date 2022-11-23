<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Syriac script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSyrc extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Syrc';
    }

    public function number()
    {
        return '135';
    }

    public function unicodeName()
    {
        return 'Syriac';
    }
}
