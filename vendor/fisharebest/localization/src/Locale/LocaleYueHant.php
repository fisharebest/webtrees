<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptHant;

/**
 * Class LocaleYueHant - Yue
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleYueHant extends LocaleYue
{
    public function endonym()
    {
        return '粤语';
    }

    public function script()
    {
        return new ScriptHant();
    }
}
