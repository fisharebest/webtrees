<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRn;

/**
 * Class LocaleRn - Rundi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ikirundi';
    }

    public function endonymSortable()
    {
        return 'IKIRUNDI';
    }

    public function language()
    {
        return new LanguageRn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
