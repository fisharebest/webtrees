<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBr;

/**
 * Class LocaleBr - Breton
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'brezhoneg';
    }

    public function endonymSortable()
    {
        return 'BREZHONEG';
    }

    public function language()
    {
        return new LanguageBr();
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
