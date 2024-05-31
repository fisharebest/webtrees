<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLt;

/**
 * Class LocaleLt - Lithuanian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLt extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'lithuanian_ci';
    }

    public function endonym()
    {
        return 'lietuvių';
    }

    public function endonymSortable()
    {
        return 'LIETUVIU';
    }

    public function language()
    {
        return new LanguageLt();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::NBSP,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
