<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsb;

/**
 * Class LocaleKsb - Shambala
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKsb extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kishambaa';
    }

    public function endonymSortable()
    {
        return 'KISHAMBAA';
    }

    public function language()
    {
        return new LanguageKsb();
    }
}
