<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEu;

/**
 * Class LocaleEu - Basque
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'euskara';
    }

    public function endonymSortable()
    {
        return 'EUSKARA';
    }

    public function language()
    {
        return new LanguageEu();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::DOT,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PERCENT . self::NBSP . '%s';
    }
}
