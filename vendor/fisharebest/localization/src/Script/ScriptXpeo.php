<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptXpeo - Representation of the Old Persian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptXpeo extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Xpeo';
    }

    public function number()
    {
        return '030';
    }

    public function unicodeName()
    {
        return 'Old_Persian';
    }
}
