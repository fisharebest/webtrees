<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGu;

/**
 * Class LocaleGu - Gujarati
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGu extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'ગુજરાતી';
    }

    public function language()
    {
        return new LanguageGu();
    }
}
