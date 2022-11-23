<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMaya - Representation of the Mayan hieroglyphs script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMaya extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Maya';
    }

    public function number()
    {
        return '090';
    }
}
