<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTi;

/**
 * Class LocaleTi - Tigrinya
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTi extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ትግርኛ';
    }

    public function language()
    {
        return new LanguageTi();
    }
}
