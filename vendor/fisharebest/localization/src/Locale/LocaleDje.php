<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDje;

/**
 * Class LocaleDje - Zarma
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDje extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Zarmaciine';
    }

    public function endonymSortable()
    {
        return 'ZARMACIINE';
    }

    public function language()
    {
        return new LanguageDje();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
