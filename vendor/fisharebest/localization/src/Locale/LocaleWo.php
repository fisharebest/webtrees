<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWo;

/**
 * Class LocaleWo - Wo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleWo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Wolof';
    }

    public function endonymSortable()
    {
        return 'WOLOF';
    }

    public function language()
    {
        return new LanguageWo();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
