<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRaj;

/**
 * Class LocaleHi - Hindi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRaj extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'राजस्थानी';
    }

    public function language()
    {
        return new LanguageRaj();
    }
}
