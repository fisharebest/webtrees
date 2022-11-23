<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSc;

/**
 * Class LocaleSc - Sardinian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSc extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'sardu';
    }

    public function endonymSortable()
    {
        return 'SARDU';
    }

    public function language()
    {
        return new LanguageSc();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
