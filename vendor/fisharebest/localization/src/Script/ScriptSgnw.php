<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSgnw - Representation of the SignWriting script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSgnw extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sgnw';
    }

    public function number()
    {
        return '095';
    }

    public function unicodeName()
    {
        return 'SignWriting';
    }
}
