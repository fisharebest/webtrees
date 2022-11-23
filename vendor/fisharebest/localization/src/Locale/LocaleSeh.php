<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSeh;

/**
 * Class LocaleSeh - Sena
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSeh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'sena';
    }

    public function endonymSortable()
    {
        return 'SENA';
    }

    public function language()
    {
        return new LanguageSeh();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
