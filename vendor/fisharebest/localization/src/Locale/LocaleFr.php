<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFr;

/**
 * Class LocaleFr - French
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'français';
    }

    public function endonymSortable()
    {
        return 'FRANCAIS';
    }

    public function language()
    {
        return new LanguageFr();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NARROW_NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
