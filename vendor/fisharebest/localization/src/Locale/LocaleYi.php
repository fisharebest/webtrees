<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYi;

/**
 * Class LocaleYi - Yiddish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleYi extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ייִדיש';
    }

    public function language()
    {
        return new LanguageYi();
    }
}
