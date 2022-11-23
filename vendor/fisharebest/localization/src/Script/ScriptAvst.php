<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAvst - Representation of the Avestan script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptAvst extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Avst';
    }

    public function number()
    {
        return '134';
    }

    public function unicodeName()
    {
        return 'Avestan';
    }
}
