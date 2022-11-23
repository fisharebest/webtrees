<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGsw;

/**
 * Class LocaleGsw - Swiss German
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGsw extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Schwiizertüütsch';
    }

    public function endonymSortable()
    {
        return 'SCHWIIZERTUUTSCH';
    }

    public function language()
    {
        return new LanguageGsw();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::APOSTROPHE,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
