<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVun;

/**
 * Class LocaleVun - Vunjo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleVun extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kyivunjo';
    }

    public function endonymSortable()
    {
        return 'KYIVUNJO';
    }

    public function language()
    {
        return new LanguageVun();
    }
}
