<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLb;

/**
 * Class LocaleLb - Luxembourgish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLb extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Lëtzebuergesch';
    }

    public function endonymSortable()
    {
        return 'LETZEBUERGESCH';
    }

    public function language()
    {
        return new LanguageLb();
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
