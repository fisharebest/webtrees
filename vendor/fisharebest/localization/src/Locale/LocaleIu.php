<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIu;

/**
 * Class LocaleIu - Inuktitut
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ᐃᓄᒃᑎᑐᑦ';
    }

    public function endonymSortable()
    {
        return 'ᐃᓄᒃᑎᑐᑦ';
    }

    public function language()
    {
        return new LanguageIu();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
