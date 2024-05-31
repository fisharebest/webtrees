<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrj - Representation of the Syriac (Western variant) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSyrj extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Syrj';
    }

    public function number()
    {
        return '137';
    }
}
