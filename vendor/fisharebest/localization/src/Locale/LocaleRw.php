<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRw;

/**
 * Class LocaleRw - Kinyarwanda
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRw extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kinyarwanda';
    }

    public function endonymSortable()
    {
        return 'KINYARWANDA';
    }

    public function language()
    {
        return new LanguageRw();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
