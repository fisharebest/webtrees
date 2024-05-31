<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAm;

/**
 * Class LocaleAm - Amharic
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAm extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'አማርኛ';
    }

    public function language()
    {
        return new LanguageAm();
    }
}
