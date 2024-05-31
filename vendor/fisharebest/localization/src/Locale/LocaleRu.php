<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRu;

/**
 * Class LocaleRu - Russian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'русский';
    }

    public function endonymSortable()
    {
        return 'РУССКИЙ';
    }

    public function language()
    {
        return new LanguageRu();
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
