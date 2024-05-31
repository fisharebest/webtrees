<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHi;

/**
 * Class LocaleHi - Hindi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHi extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'हिन्दी';
    }

    public function language()
    {
        return new LanguageHi();
    }
}
