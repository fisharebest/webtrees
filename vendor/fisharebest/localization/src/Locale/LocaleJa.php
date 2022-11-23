<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJa;

/**
 * Class LocaleJa - Japanese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleJa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return '日本語';
    }

    public function language()
    {
        return new LanguageJa();
    }
}
