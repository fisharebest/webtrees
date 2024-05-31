<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNso;

/**
 * Class LocaleNso - Northern Sotho
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNso extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Sesotho sa Leboa';
    }

    public function endonymSortable()
    {
        return 'SESOTHO SA LEBOA';
    }

    public function language()
    {
        return new LanguageNso();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
