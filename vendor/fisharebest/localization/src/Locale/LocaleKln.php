<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKln;

/**
 * Class LocaleKln - Kalenjin
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKln extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kalenjin';
    }

    public function endonymSortable()
    {
        return 'KALENJIN';
    }

    public function language()
    {
        return new LanguageKln();
    }
}
