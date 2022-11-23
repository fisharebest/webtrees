<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEl;

/**
 * Class LocaleEl - Greek
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEl extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ελληνικά';
    }

    public function endonymSortable()
    {
        return 'ΕΛΛΗΝΙΚΆ';
    }

    public function language()
    {
        return new LanguageEl();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
