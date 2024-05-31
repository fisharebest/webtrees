<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLatn - Representation of the Latin script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLatn extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Latn';
    }

    public function number()
    {
        return '215';
    }

    public function unicodeName()
    {
        return 'Latin';
    }
}
