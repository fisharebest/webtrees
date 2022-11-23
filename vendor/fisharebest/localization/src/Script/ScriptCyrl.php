<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCyrl - Representation of the Cyrillic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptCyrl extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Cyrl';
    }

    public function number()
    {
        return '220';
    }

    public function unicodeName()
    {
        return 'Cyrillic';
    }
}
