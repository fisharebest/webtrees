<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVe;

/**
 * Class LocaleVe - Venda
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleVe extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tshivenḓa';
    }

    public function endonymSortable()
    {
        return 'TSHIVENDA';
    }

    public function language()
    {
        return new LanguageVe();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
