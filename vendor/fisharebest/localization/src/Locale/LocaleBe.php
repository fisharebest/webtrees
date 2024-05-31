<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBe;

/**
 * Class LocaleBe - Belarusian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBe extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'беларуская';
    }

    public function endonymSortable()
    {
        return 'БЕЛАРУСКАЯ';
    }

    public function language()
    {
        return new LanguageBe();
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
