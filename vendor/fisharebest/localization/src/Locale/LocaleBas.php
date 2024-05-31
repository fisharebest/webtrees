<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBas;

/**
 * Class LocaleBas - Basaa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBas extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ɓàsàa';
    }

    public function endonymSortable()
    {
        return 'BASAA';
    }

    public function language()
    {
        return new LanguageBas();
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
