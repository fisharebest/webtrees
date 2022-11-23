<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageOsa;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LocaleOs - Osage
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleOsa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Osage';
    }

    public function endonymSortable()
    {
        return 'OSAGE';
    }

    public function language()
    {
        return new LanguageOsa();
    }

    public function territory()
    {
        return new TerritoryUS();
    }
}
