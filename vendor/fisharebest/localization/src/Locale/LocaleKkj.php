<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKkj;

/**
 * Class LocaleKkj - Kako
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKkj extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'kakɔ';
    }

    public function endonymSortable()
    {
        return 'KAKO';
    }

    public function language()
    {
        return new LanguageKkj();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
