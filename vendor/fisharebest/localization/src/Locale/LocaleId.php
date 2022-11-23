<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageId;

/**
 * Class LocaleId - Indonesian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleId extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Indonesia';
    }

    public function endonymSortable()
    {
        return 'INDONESIA';
    }

    public function language()
    {
        return new LanguageId();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
