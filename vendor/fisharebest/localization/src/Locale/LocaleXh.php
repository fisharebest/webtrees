<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageXh;

/**
 * Class LocaleXh - Xhosa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleXh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'IsiXhosa';
    }

    public function endonymSortable()
    {
        return 'XHOSA';
    }

    public function language()
    {
        return new LanguageXh();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
