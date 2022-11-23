<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBs;

/**
 * Class LocaleBs - Bosnian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBs extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'bosanski';
    }

    public function endonymSortable()
    {
        return 'BOSANSKI';
    }

    public function language()
    {
        return new LanguageBs();
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
