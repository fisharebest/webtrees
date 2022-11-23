<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRo;

/**
 * Class LocaleRo - Romanian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRo extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'romanian_ci';
    }

    public function endonym()
    {
        return 'română';
    }

    public function endonymSortable()
    {
        return 'ROMANA';
    }

    public function language()
    {
        return new LanguageRo();
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
