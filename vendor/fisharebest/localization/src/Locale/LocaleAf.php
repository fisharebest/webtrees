<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAf;

/**
 * Class LocaleAf - Afrikaans
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAf extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Afrikaans';
    }

    public function endonymSortable()
    {
        return 'AFRIKAANS';
    }

    public function language()
    {
        return new LanguageAf();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
