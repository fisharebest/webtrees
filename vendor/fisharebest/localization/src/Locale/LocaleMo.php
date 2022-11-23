<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMo;

/**
 * Class LocaleIt - Italian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'limba moldovenească';
    }

    public function endonymSortable()
    {
        return 'LIMBA MOLDOVENEASCĂ';
    }

    public function language()
    {
        return new LanguageMo();
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
