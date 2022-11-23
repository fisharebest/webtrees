<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptHant;

/**
 * Class LocaleZhHant - Traditional Chinese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZhHant extends LocaleZh
{
    public function endonym()
    {
        return '繁體中文';
    }

    protected function minimumGroupingDigits()
    {
        return 3;
    }

    public function script()
    {
        return new ScriptHant();
    }
}
