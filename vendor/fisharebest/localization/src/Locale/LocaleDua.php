<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDua;

/**
 * Class LocaleDua - Duala
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDua extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'duálá';
    }

    public function endonymSortable()
    {
        return 'DUALA';
    }

    public function language()
    {
        return new LanguageDua();
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
