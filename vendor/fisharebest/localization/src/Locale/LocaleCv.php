<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCv;

/**
 * Class LocaleCv - Chuvash
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCv extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'чӑваш';
    }

    public function endonymSortable()
    {
        return 'ЧӐВАШ';
    }

    public function language()
    {
        return new LanguageCv();
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
