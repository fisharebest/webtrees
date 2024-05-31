<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKore - Representation of the Korean (alias for Hangul + Han) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKore extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Kore';
    }

    public function number()
    {
        return '287';
    }
}
