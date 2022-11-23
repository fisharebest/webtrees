<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKn;

/**
 * Class LocaleKn - Kannada
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ಕನ್ನಡ';
    }

    public function language()
    {
        return new LanguageKn();
    }
}
