<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHaw;

/**
 * Class LocaleHaw - Hawaiian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHaw extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ʻŌlelo Hawaiʻi';
    }

    public function endonymSortable()
    {
        return 'OLELO HAWAII';
    }

    public function language()
    {
        return new LanguageHaw();
    }
}
