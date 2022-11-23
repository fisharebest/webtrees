<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLn;

/**
 * Class LocaleLn - Lingala
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'lingála';
    }

    public function endonymSortable()
    {
        return 'LINGALA';
    }

    public function language()
    {
        return new LanguageLn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
