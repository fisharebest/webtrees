<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKo;

/**
 * Class LocaleKo - Korean
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return '한국어';
    }

    public function language()
    {
        return new LanguageKo();
    }
}
