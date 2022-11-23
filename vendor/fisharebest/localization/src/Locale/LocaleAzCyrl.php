<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptCyrl;

/**
 * Class LocaleAzCyrl
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAzCyrl extends LocaleAz
{
    public function script()
    {
        return new ScriptCyrl();
    }
}
