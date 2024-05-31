<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePt;

/**
 * Class LocalePt - Portuguese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePt extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'português';
    }

    public function endonymSortable()
    {
        return 'PORTUGUES';
    }

    public function language()
    {
        return new LanguagePt();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
