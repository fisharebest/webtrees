<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKu;

/**
 * Class LocaleKu - Kurdish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'kurdî';
    }

    public function endonymSortable()
    {
        return 'KURDI';
    }

    public function language()
    {
        return new LanguageKu();
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
        return self::PERCENT . '%s';
    }
}
