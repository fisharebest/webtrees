<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLu;

/**
 * Class LocaleLu - Luba-Katanga
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tshiluba';
    }

    public function endonymSortable()
    {
        return 'TSHILUBA';
    }

    public function language()
    {
        return new LanguageLu();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
