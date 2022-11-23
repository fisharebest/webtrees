<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDa;

/**
 * Class LocaleDa - Danish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDa extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'danish_ci';
    }

    public function endonym()
    {
        return 'dansk';
    }

    public function endonymSortable()
    {
        return 'DANSK';
    }

    public function language()
    {
        return new LanguageDa();
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
